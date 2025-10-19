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
$certificate_type = 'death_certificate';

if (!$death_record_id || !$issued_by) {
    jsonResponse("error", "Invalid or missing input");
}

try {
    $pdo->beginTransaction();

    // Generate certificate number format: DC-YYYY-0001
    $certificate_number = 'DC-' . date('Y') . '-' . str_pad($death_record_id, 4, '0', STR_PAD_LEFT);

    // Insert into certificates table
    $stmt = $pdo->prepare("
    INSERT INTO certificates (pet_id, death_record_id, certificate_type, certificate_number, issued_by, certificate_issued)
    SELECT dr.pet_id, dr.id, ?, ?, ?, 1
    FROM death_records dr
    WHERE dr.id = ?
    ON DUPLICATE KEY UPDATE
        certificate_type = VALUES(certificate_type),
        certificate_number = VALUES(certificate_number),
        issued_by = VALUES(issued_by),
        certificate_issued = 1,
        certificate_date = NOW();
    ");
    $stmt->execute([$certificate_type, $certificate_number, $issued_by, $death_record_id]);


    $pdo->commit();

    // Regenerate CSRF token for security
    SessionManager::regenerateCsrfToken();

    jsonResponse("success", "Certificate issued successfully", [
        "certificate_number" => $certificate_number,
        "csrf_token" => $_SESSION['csrf_token']
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Certificate issue failed: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    exit;

}

ob_end_flush();