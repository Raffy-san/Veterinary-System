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
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="death-certificate.pdf"');

// Get current logged-in user
$current_user = SessionManager::getUser($pdo);
if (!$current_user) {
    jsonResponse("error", "User not logged in");
}

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse("error", "Invalid request method");
}

// CSRF Protection
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    jsonResponse("error", "Invalid CSRF token");
}

// Validate inputs
$death_record_id = filter_input(INPUT_POST, 'death_record_id', FILTER_VALIDATE_INT);
$issued_by = $current_user['user_id'];

if (!$death_record_id || !$issued_by) {
    jsonResponse("error", "Invalid or missing input");
}

try {
    $pdo->beginTransaction();

    // Generate certificate number format: DC-YYYY-0001
    $certificate_number = 'DC-' . date('Y') . '-' . str_pad($death_record_id, 4, '0', STR_PAD_LEFT);

    // Update death record
    $stmt = $pdo->prepare("
        UPDATE death_records 
        SET certificate_issued = 1, 
            certificate_number = ?, 
            certificate_date = NOW(), 
            issued_by = ? 
        WHERE id = ?
    ");
    $stmt->execute([$certificate_number, $issued_by, $death_record_id]);

    $pdo->commit();

    // Generate new CSRF token for security
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    jsonResponse("success", "Certificate issued successfully", [
        "certificate_number" => $certificate_number,
        "csrf_token" => $_SESSION['csrf_token']
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Certificate issue failed: " . $e->getMessage());
    jsonResponse("error", "An error occurred while issuing the certificate");
}

ob_end_flush();
