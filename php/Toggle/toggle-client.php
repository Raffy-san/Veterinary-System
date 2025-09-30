<?php
ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');

include_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../functions/session.php';
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

$userID = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
$action = $_POST['action'] ?? '';

if (!$userID || !in_array($action, ['activate', 'deactivate'])) {
    jsonResponse("error", "Invalid input");
}

try {
    $pdo->beginTransaction();

    if ($action === 'activate') {
        $stmt1 = $pdo->prepare("UPDATE users SET status = 'Active' WHERE id = ?");
        $stmt1->execute([$userID]);

        $stmt2 = $pdo->prepare("UPDATE owners SET status = 'Active' WHERE user_id = ?");
        $stmt2->execute([$userID]);
    } else {
        $stmt1 = $pdo->prepare("UPDATE users SET status = 'Inactive' WHERE id = ?");
        $stmt1->execute([$userID]);

        $stmt2 = $pdo->prepare("UPDATE owners SET status = 'Inactive' WHERE user_id = ?");
        $stmt2->execute([$userID]);
    }

    $pdo->commit();

    // regenerate CSRF token for next request
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    jsonResponse("success", "Client " . $action . "d successfully", [
        "csrf_token" => $_SESSION['csrf_token']
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Toggle client failed: " . $e->getMessage());
    jsonResponse("error", "An error occurred while updating client status");
}

ob_end_flush();
