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

$petID = filter_input(INPUT_POST, 'pet_id', FILTER_VALIDATE_INT);
$status = $_POST['status'] ?? '';
$death_reason = trim($_POST['death_reason'] ?? '');
$death_date = $_POST['death_date'] ?? '';

if (!$petID || !in_array($status, ['Alive', 'Dead'])) {
    jsonResponse("error", "Invalid input");
}

try {
    $pdo->beginTransaction();

    if ($status === 'Alive') {
        // Update only the status
        $stmt = $pdo->prepare("UPDATE pets SET status = ?, death_reason = NULL, death_date = NULL WHERE id = ?");
        $stmt->execute([$status, $petID]);
    } else {
        // Update status + death details
        $stmt = $pdo->prepare("UPDATE pets SET status = ?, death_reason = ?, death_date = ? WHERE id = ?");
        $stmt->execute([$status, $death_reason, $death_date, $petID]);
    }

    $pdo->commit();

    // Regenerate CSRF token for next request
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    jsonResponse("success", "Pet status updated successfully", [
        "csrf_token" => $_SESSION['csrf_token']
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Toggle pet failed: " . $e->getMessage());
    jsonResponse("error", "An error occurred while updating pet status");
}

ob_end_flush();
