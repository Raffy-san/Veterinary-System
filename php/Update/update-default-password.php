<?php
ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');

include_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../functions/session.php';
require_once __DIR__ . '/../../functions/response.php';
require_once __DIR__ . '/../../functions/settings.php'; // 👈 for getDefaultPassword() & updateDefaultPassword()

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

$newAdminPassword = trim($_POST['admin_password'] ?? '');
$newOwnerPassword = trim($_POST['owner_password'] ?? '');

if (empty($newAdminPassword) || empty($newOwnerPassword)) {
    jsonResponse("error", "Both default passwords are required");
}

try {
    $adminUpdated = updateDefaultPassword($pdo, 'admin', $newAdminPassword);
    $ownerUpdated = updateDefaultPassword($pdo, 'owner', $newOwnerPassword);

    if ($adminUpdated && $ownerUpdated) {
        SessionManager::regenerateCsrfToken();
        jsonResponse("success", "Default passwords updated successfully", [
            "csrf_token" => $_SESSION['csrf_token']
        ]);
    } else {
        jsonResponse("error", "Failed to update one or more default passwords");
    }
} catch (Exception $e) {
    error_log("Update default password failed: " . $e->getMessage());
    jsonResponse("error", "An error occurred while updating defaults");
}

ob_end_flush();
