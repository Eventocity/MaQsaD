<?php
session_start();
require_once '../auth/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'beneficiary') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$service_id = $data['service_id'] ?? null;

if (!$service_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Service ID is required']);
    exit;
}

try {
    // Get beneficiary ID
    $stmt = $conn->prepare("
        SELECT id 
        FROM beneficiaries 
        WHERE user_id = :user_id
    ");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $beneficiary = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$beneficiary) {
        throw new Exception('Beneficiary not found');
    }

    // Create service request
    $stmt = $conn->prepare("
        INSERT INTO service_requests (
            beneficiary_id, 
            provider_id,
            service_type,
            status
        ) VALUES (
            :beneficiary_id,
            :provider_id,
            (
                SELECT JSON_UNQUOTE(JSON_EXTRACT(services, '$[0]'))
                FROM providers
                WHERE id = :provider_id2
            ),
            'pending'
        )
    ");
    
    $stmt->bindParam(':beneficiary_id', $beneficiary['id']);
    $stmt->bindParam(':provider_id', $service_id);
    $stmt->bindParam(':provider_id2', $service_id);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Service request created successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
