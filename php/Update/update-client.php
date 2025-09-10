<?php
ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'C:\xampp\htdocs\Veterinary-System\logs\php_errors.log');

include_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../functions/session.php';
require_once __DIR__ . '/../../helpers/fetch.php';
require_once __DIR__ . '/../../functions/crud.php';
require_once __DIR__ . '/../../functions/response.php';

SessionManager::requireLogin();
SessionManager::requireRole('admin');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse("error", "Invalid request method");
}

if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    jsonResponse("error", "Invalid CSRF token");
}

// Start with owner id (must always exist)
$data = [
    'owner_id' => filter_input(INPUT_POST, 'owner_id', FILTER_VALIDATE_INT),
];

// Validate owner ID
if (!$data['owner_id']) {
    jsonResponse("error", "Invalid or missing owner ID");
}

if (!empty($_POST['name'])) {
    $name = trim($_POST['name']);
    if (strlen($name) < 2) {
        jsonResponse("error", "Name must be at least 2 characters long");
    }
    $data['name'] = trim($_POST['name']);
}

if (!empty($_POST['password'])) {
    $password = trim($_POST['password']);
    $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    if (!preg_match($passwordRegex, $password)) {
        jsonResponse("error", "Password must be at least 8 characters and include uppercase, lowercase, number, and special character");
    }
    $data['password'] = $password; // hash inside updateClient
}

if (!empty($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        jsonResponse("error", "Invalid email format");
    }
    $data['email'] = $email;
}

if (!empty($_POST['phone'])) {
    $data['phone'] = preg_replace('/\D/', '', $_POST['phone']); // keep only digits
}

if (!empty($_POST['emergency'])) {
    $data['emergency_contact'] = preg_replace('/\D/', '', $_POST['emergency']);
}

if (!empty($_POST['address'])) {
    $data['address'] = trim($_POST['address']);
}

// Run update
$result = updateClient($pdo, $data);
$resultData = json_decode($result, true);

// If success, regenerate CSRF token for next requests
if ($resultData && $resultData['status'] === "success") {
    unset($_SESSION['csrf_token']);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    jsonResponse("success", "Client account updated successfully", [
        "csrf_token" => $_SESSION['csrf_token']
    ]);
} else {
    jsonResponse("error", $resultData['message'] ?? "Failed to update client account");
}

ob_end_flush();
