<?php
// auth/logout.php

// Disable error display in response
error_reporting(0);
ini_set('display_errors', 0);

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/php_errors.log');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON response header
header('Content-Type: application/json');

$response = [
    'success' => true,
    'message' => 'Logged out successfully',
    'redirect' => '../index.html'
];

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Return JSON response
echo json_encode($response);
exit;
?>
