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
    'success' => false,
    'message' => '',
    'redirect' => '',
    'user' => null
];

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    error_log("Login attempt - Input: " . print_r($input, true));
    
    if (!$input) {
        throw new Exception('Please enter username and password');
    }

    $username = trim($input['username'] ?? '');
    $password = trim($input['password'] ?? '');

    if (empty($username) || empty($password)) {
        throw new Exception('Please enter username and password');
    }

    // Get user data
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
        WHERE u.username = ?
    ");
    
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    error_log("User data: " . print_r($user, true));

    if (!$user) {
        throw new Exception('User not found. Please check your username or sign up.');
    }

    if (!password_verify($password, $user['password'])) {
        throw new Exception('Incorrect password. Please try again.');
    }

    // Set session data
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['profile_id'] = $user['profile_id'];
    $_SESSION['display_name'] = $user['display_name'];

    // Prepare user data for response
    $userData = [
        'id' => $user['id'],
        'username' => $user['username'],
        'role' => $user['role'],
        'profile_id' => $user['profile_id'],
        'display_name' => $user['display_name']
    ];

    $response['success'] = true;
    $response['message'] = 'Login successful';
    $response['redirect'] = 'dashboard.php'; 
    $response['user'] = $userData;

    error_log("Login successful for user: " . $user['username']);

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
