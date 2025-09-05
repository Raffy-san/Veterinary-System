<?php
ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'C:\xampp\htdocs\Veterinary-System\logs\php_errors.log');

include_once '../config/config.php';
require_once '../functions/session.php';
require_once '../functions/crud.php';
require_once '../functions/response.php';

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

// Validate record ID
$recordID = filter_input(INPUT_POST, 'record_id', FILTER_VALIDATE_INT);
if (!$recordID) {
    jsonResponse("error", "Invalid or missing record ID");
}

try {
    $result = deleteMedicalRecord($pdo, $recordID);
    $resultData = json_decode($result, true);

    if ($resultData && $resultData['status'] === "success") {
        // Rotate CSRF token for security
        unset($_SESSION['csrf_token']);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        jsonResponse("success", "Medical record deleted successfully", [
            "csrf_token" => $_SESSION['csrf_token']
        ]);
    } else {
        jsonResponse("error", $resultData['message'] ?? "Failed to delete record");
    }
} catch (Exception $e) {
    error_log("Delete record failed: " . $e->getMessage());
    jsonResponse("error", "An error occurred while deleting the record");
}

ob_end_flush();
