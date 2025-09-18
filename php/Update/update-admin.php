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

$data = [
    'admin_id' => filter_input(INPUT_POST, 'admin_id', FILTER_VALIDATE_INT),
    'email' => strtolower(trim($_POST['email'] ?? '')),
    'password' => trim($_POST['password'] ?? ''),
    'current_password' => trim($_POST['current_password'] ?? '')
];

// Validate admin ID
if (!$data['admin_id']) {
    jsonResponse("error", "Invalid or missing admin ID");
}

// Require current password
if (empty($data['current_password'])) {
    jsonResponse("error", "Current password is required");
}

// Get current admin info
$admin = SessionManager::getUser($pdo);
if (!$admin || !isset($admin['password'])) {
    jsonResponse("error", "Unable to verify current password. User data not found.");
}

// Verify current password
if (!password_verify($data['current_password'], $admin['password'])) {
    jsonResponse("error", "Current password is incorrect");
}

// Validate email
if (empty($data['email'])) {
    jsonResponse("error", "Email is required");
}
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    jsonResponse("error", "Invalid email format");
}

// Ensure email is unique
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
$stmt->execute([$data['email'], $data['admin_id']]);
if ($stmt->fetchColumn() > 0) {
    jsonResponse("error", "Email is already taken");
}

// Validate password only (hashing will be done inside updateAdmin)
if (!empty($data['password'])) {
    $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])(?!.*\s).{8,}$/';
    if (!preg_match($passwordRegex, $data['password'])) {
        jsonResponse("error", "Password must be at least 8 characters and include uppercase, lowercase, number, and special character");
    }
}

// Remove current password before passing to updateAdmin
unset($data['current_password']);

// Run update
$result = updateAdmin($pdo, $data);

// Decode updateAdmin result
$resultData = json_decode($result, true);

// If success, regenerate CSRF token for next requests
if ($resultData && $resultData['status'] === "success") {
    unset($_SESSION['csrf_token']);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    jsonResponse("success", "Admin account updated successfully", [
        "csrf_token" => $_SESSION['csrf_token']
    ]);
} else {
    jsonResponse("error", $resultData['message'] ?? "Failed to update admin account");
}

ob_end_flush();

