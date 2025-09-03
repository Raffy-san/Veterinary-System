<?php
ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'C:\xampp\htdocs\Veterinary-System\logs\php_errors.log');

include_once '../config/config.php';
require_once '../functions/session.php';
require_once '../functions/crud.php';

SessionManager::requireLogin();
SessionManager::requireRole('admin');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    echo json_encode(["status" => "error", "message" => "Invalid CSRF token"]);
    exit;
}

$data = [
    'admin_id' => filter_input(INPUT_POST, 'admin_id', FILTER_VALIDATE_INT),
    'username' => trim($_POST['username'] ?? ''),
    'password' => trim($_POST['password'] ?? ''),
    'current_password' => trim($_POST['current_password'] ?? '')
];

$response = ['status' => 'error', 'message' => ''];

// Validate admin ID
if (!$data['admin_id']) {
    $response['message'] = 'Invalid or missing admin ID';
    echo json_encode($response);
    exit;
}

// Require current password
if (empty($data['current_password'])) {
    $response['message'] = 'Current password is required';
    echo json_encode($response);
    exit;
}

// Get current admin info
$admin = SessionManager::getUser($pdo);
if (!$admin || !isset($admin['password'])) {
    $response['message'] = 'Unable to verify current password. User data not found.';
    echo json_encode($response);
    exit;
}

// Verify current password
if (!password_verify($data['current_password'], $admin['password'])) {
    $response['message'] = 'Current password is incorrect';
    echo json_encode($response);
    exit;
}

// Validate username
if (empty($data['username']) || strlen($data['username']) < 3) {
    $response['message'] = 'Username must be at least 3 characters long';
    echo json_encode($response);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_-]{3,50}$/', $data['username'])) {
    $response['message'] = 'Username can only contain letters, numbers, underscores, and hyphens';
    echo json_encode($response);
    exit;
}

// Ensure username is unique
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
$stmt->execute([$data['username'], $data['admin_id']]);
if ($stmt->fetchColumn() > 0) {
    $response['message'] = 'Username is already taken';
    echo json_encode($response);
    exit;
}

// Validate password only (hashing will be done inside updateAdmin)
if (!empty($data['password'])) {
    $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    if (!preg_match($passwordRegex, $data['password'])) {
        $response['message'] = 'Password must be at least 8 characters and include uppercase, lowercase, number, and special character';
        echo json_encode($response);
        exit;
    }
}

// Remove current password before passing to updateAdmin
unset($data['current_password']);

// Run update
$result = updateAdmin($pdo, $data);

echo $result;
ob_end_flush();
