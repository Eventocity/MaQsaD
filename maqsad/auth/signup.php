<?php
// Disable error display in response
error_reporting(0);
ini_set('display_errors', 0);

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/php_errors.log');

// Start session and include database connection
session_start();
require_once 'connect.php';

// Set JSON response header
header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'redirect' => ''];

try {
    // Log received data for debugging
    error_log("Signup request received");
    error_log("POST data: " . print_r($_POST, true));
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Common fields for both roles
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? '');

    // Get email based on role
    if ($role === 'provider') {
        $email = trim($_POST['company_email'] ?? '');
    } else {
        $email = trim($_POST['email'] ?? '');
    }
    
    // Validate required fields
    if (empty($username)) {
        throw new Exception("Username is required");
    }
    if (empty($password)) {
        throw new Exception("Password is required");
    }
    if (empty($email)) {
        throw new Exception("Email is required");
    }
    if (!in_array($role, ['provider', 'beneficiary'])) {
        throw new Exception("Invalid role specified");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        throw new Exception("Username already exists");
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception("Email already exists");
    }

    // Begin transaction
    $conn->beginTransaction();

    try {
        // Insert into users table
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->execute([$username, $hashedPassword, $email, $role]);
        
        $userId = $conn->lastInsertId();
        error_log("Created user with ID: $userId");

        // Additional fields based on role
        if ($role === 'provider') {
            $companyName = trim($_POST['company_name'] ?? '');
            $serviceName = trim($_POST['service_name'] ?? '');
            $serviceDesc = trim($_POST['service_description'] ?? '');

            if (empty($companyName)) throw new Exception("Company name is required");
            if (empty($serviceName)) throw new Exception("Service name is required");
            if (empty($serviceDesc)) throw new Exception("Service description is required");

            $stmt = $conn->prepare("
                INSERT INTO providers (
                    user_id, company_name, company_email, service_name, 
                    service_description, services
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            $services = '[]'; // Default empty JSON array
            $stmt->execute([
                $userId, 
                $companyName, 
                $email, 
                $serviceName, 
                $serviceDesc,
                $services
            ]);
            error_log("Provider data inserted successfully");

        } else {
            $fullName = trim($_POST['full_name'] ?? '');
            if (empty($fullName)) throw new Exception("Full name is required");

            $stmt = $conn->prepare("
                INSERT INTO beneficiaries (
                    user_id, full_name, company_name, business_industry,
                    preferred_provider, service_interests, other_service, needs
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $serviceInterests = '[]'; // Default empty JSON array
            $stmt->execute([
                $userId,
                $fullName,
                $_POST['beneficiary_company'] ?? null,
                $_POST['business_industry'] ?? null,
                $_POST['preferred_provider'] ?? null,
                $serviceInterests,
                null, // other_service
                null  // needs
            ]);
            error_log("Beneficiary data inserted successfully");
        }

        // If we got here, commit the transaction
        $conn->commit();
        error_log("Transaction committed successfully");

        // Set success response
        $response['success'] = true;
        $response['message'] = "Registration successful! Please log in.";
        $response['redirect'] = '../index.html';

    } catch (Exception $e) {
        // If anything went wrong, roll back the transaction
        $conn->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    error_log("Error in signup: " . $e->getMessage());
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
exit;
?>
