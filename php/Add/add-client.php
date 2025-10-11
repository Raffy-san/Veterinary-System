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

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse("error", "Invalid request method");
}

// CSRF protection
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    jsonResponse("error", "Invalid CSRF token");
}

// Collect & sanitize data
$data = [
    'name' => ucwords(strtolower(trim($_POST['name'] ?? ''))),
    'password' => trim($_POST['password'] ?? ''), // raw password, hashing handled in addClient()
    'email' => filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL),
    'phone' => isset($_POST['phone']) ? preg_replace('/\D/', '', $_POST['phone']) : '',
    'emergency' => isset($_POST['emergency']) ? preg_replace('/\D/', '', $_POST['emergency']) : '',
    'address' => trim($_POST['address'] ?? '')
];

// Basic validation
if (empty($data['password']) || !$data['email']) {
    jsonResponse("error", "Valid email and password are required.");
}

try {
    $result = addClient($pdo, $data);
    $resultData = json_decode($result, true);

    if ($resultData && $resultData['status'] === "success") {
        // Regenerate CSRF token
        unset($_SESSION['csrf_token']);
        // Regenerate CSRF token for security
        SessionManager::regenerateCsrfToken();

        jsonResponse("success", "Client added successfully", [
            "csrf_token" => $_SESSION['csrf_token'],
        ]);
    } else {
        jsonResponse("error", $resultData['message'] ?? "Failed to add client");
    }
} catch (Exception $e) {
    error_log("Add client failed: " . $e->getMessage());
    jsonResponse("error", "An error occurred while adding client");
}

ob_end_flush();
