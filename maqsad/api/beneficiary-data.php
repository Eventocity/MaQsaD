<?php
session_start();
require_once '../auth/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'beneficiary') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Get beneficiary's needs
    $stmt = $conn->prepare("
        SELECT needs 
        FROM beneficiaries 
        WHERE user_id = :user_id
    ");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $beneficiary = $stmt->fetch(PDO::FETCH_ASSOC);
    $needs = json_decode($beneficiary['needs'] ?? '[]');

    // Get available services
    $stmt = $conn->prepare("
        SELECT p.id, p.services, p.location, u.username as provider_name
        FROM providers p
        JOIN users u ON p.user_id = u.id
        WHERE JSON_OVERLAPS(p.services, :needs)
    ");
    $stmt->bindParam(':needs', json_encode($needs));
    $stmt->execute();
    $available_services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format services for display
    $formatted_services = [];
    foreach ($available_services as $provider) {
        $services = json_decode($provider['services'], true);
        foreach ($services as $service) {
            if (in_array($service, $needs)) {
                $formatted_services[] = [
                    'id' => $provider['id'],
                    'provider_name' => $provider['provider_name'],
                    'type' => $service,
                    'location' => $provider['location']
                ];
            }
        }
    }

    echo json_encode([
        'success' => true,
        'needs' => $needs,
        'available_services' => $formatted_services
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
