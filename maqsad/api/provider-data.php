<?php
session_start();
require_once '../auth/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Get provider's services
    $stmt = $conn->prepare("
        SELECT services 
        FROM providers 
        WHERE user_id = :user_id
    ");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $provider = $stmt->fetch(PDO::FETCH_ASSOC);
    $services = json_decode($provider['services'] ?? '[]');

    // Get service requests
    $stmt = $conn->prepare("
        SELECT sr.*, b.full_name as beneficiary_name
        FROM service_requests sr
        JOIN providers p ON sr.provider_id = p.id
        JOIN beneficiaries b ON sr.beneficiary_id = b.id
        WHERE p.user_id = :user_id
        ORDER BY sr.created_at DESC
    ");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'services' => $services,
        'requests' => $requests
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
