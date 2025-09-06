<?php
ob_start();
ini_set('display_errors', 0); // don't show errors to browser
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log'); // adjust path to logs

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

// Validate pet ID
$petID = filter_input(INPUT_POST, 'pet_id', FILTER_VALIDATE_INT);
if (!$petID) {
    jsonResponse("error", "Invalid or missing record ID");
}

try {
    $deleted = deletePet($pdo, $petID);

    if ($deleted) {
        // regenerate CSRF token for next request
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        jsonResponse("success", "Medical record deleted successfully", [
            "csrf_token" => $_SESSION['csrf_token']
        ]);
    } else {
        jsonResponse("error", "Pet not found or could not be deleted");
    }
} catch (Exception $e) {
    error_log("Delete pet failed: " . $e->getMessage());
    jsonResponse("error", "An error occurred while deleting the record");
}

ob_end_flush();
