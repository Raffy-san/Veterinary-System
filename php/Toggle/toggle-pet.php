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
$death_time = $_POST['death_time'] ?? null; // optional
$location_of_death = trim($_POST['location_of_death'] ?? '');
$recorded_by = trim($_POST['recorded_by'] ?? '');
$remarks = trim($_POST['remarks'] ?? '');

if (!$petID || !in_array($status, ['Alive', 'Dead'])) {
    jsonResponse("error", "Invalid input");
}

try {
    $pdo->beginTransaction();

    if ($status === 'Alive') {
        // Revert pet to alive
        $stmt = $pdo->prepare("UPDATE pets SET status = ? WHERE id = ?");
        $stmt->execute([$status, $petID]);

        // Optionally remove the death record
        $del = $pdo->prepare("DELETE FROM death_records WHERE pet_id = ?");
        $del->execute([$petID]);
    } else {
        // Update pet status
        $stmt = $pdo->prepare("UPDATE pets SET status = ? WHERE id = ?");
        $stmt->execute([$status, $petID]);

        // Check if a record already exists
        $check = $pdo->prepare("SELECT id FROM death_records WHERE pet_id = ?");
        $check->execute([$petID]);
        $existing = $check->fetchColumn();

        if (!$existing) {
            // Insert into death_records
            $insert = $pdo->prepare("
                INSERT INTO death_records 
                    (pet_id, date_of_death, time_of_death, cause_of_death, location_of_death, remarks, recorded_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $insert->execute([$petID, $death_date, $death_time, $death_reason, $location_of_death, $remarks, $recorded_by]);
        } else {
            // Update if already exists
            $update = $pdo->prepare("
                UPDATE death_records 
                SET date_of_death = ?, time_of_death = ?, cause_of_death = ?, location_of_death = ?, remarks = ?, recorded_by = ?, updated_at = NOW()
                WHERE pet_id = ?
            ");
            $update->execute([$death_date, $death_time, $death_reason, $location_of_death, $remarks, $recorded_by, $petID]);
        }
    }

    $pdo->commit();

    SessionManager::regenerateCsrfToken();

    echo json_encode([
        'status' => 'success',
        'message' => 'Pet status updated successfully.',
        'csrf_token' => $_SESSION['csrf_token']
    ]);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Toggle pet failed: " . $e->getMessage());
    jsonResponse("error", "An error occurred while updating pet status");
}

ob_end_flush();
