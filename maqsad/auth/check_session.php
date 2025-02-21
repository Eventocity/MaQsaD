<?php
// Disable error display in response
error_reporting(0);
ini_set('display_errors', 0);

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/php_errors.log');

session_start();
require_once 'connect.php';

header('Content-Type: application/json');

$response = [
    'loggedIn' => false,
    'user' => null
];

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Not logged in');
    }

    // Get fresh user data
    $stmt = $conn->prepare("
        SELECT u.*, 
               CASE 
                   WHEN u.role = 'provider' THEN p.id 
                   WHEN u.role = 'beneficiary' THEN b.id 
               END as profile_id,
               CASE 
                   WHEN u.role = 'provider' THEN p.company_name 
                   WHEN u.role = 'beneficiary' THEN b.full_name 
               END as display_name
        FROM users u
        LEFT JOIN providers p ON u.id = p.user_id AND u.role = 'provider'
        LEFT JOIN beneficiaries b ON u.id = b.user_id AND u.role = 'beneficiary'
        WHERE u.id = ?
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('User not found');
    }

    // Prepare user data for response
    $userData = [
        'id' => $user['id'],
        'username' => $user['username'],
        'role' => $user['role'],
        'profile_id' => $user['profile_id'],
        'display_name' => $user['display_name']
    ];

    $response['loggedIn'] = true;
    $response['user'] = $userData;

} catch (Exception $e) {
    error_log("Session check error: " . $e->getMessage());
    // Clear session if there's an error
    session_destroy();
}

echo json_encode($response);
?>
