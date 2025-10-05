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

$data = [
    'pet_id' => filter_input(INPUT_POST, 'patient', FILTER_VALIDATE_INT),
    'visit_date' => $_POST['visit_date'] ?? '',
    'visit_time' => $_POST['visit_time'] ?? '',
    'visit_type' => trim($_POST['visit_type'] ?? ''),
    'veterinarian' => trim(ucwords(strtolower($_POST['veterinarian'] ?? ''))),
    'weight' => ($_POST['weight'] !== '' ? filter_var($_POST['weight'], FILTER_VALIDATE_FLOAT) : null),
    'weight_unit' => trim($_POST['weight_unit'] ?? ''),
    'temperature' => ($_POST['temperature'] !== '' ? filter_var($_POST['temperature'], FILTER_VALIDATE_FLOAT) : null),
    'temp_unit' => trim($_POST['temp_unit'] ?? ''),
    'diagnosis' => trim($_POST['diagnosis'] ?? ''),
    'treatment' => trim($_POST['treatment'] ?? ''),
    'medications' => trim($_POST['medications'] ?? ''),
    'notes' => trim($_POST['notes'] ?? ''),
    'follow_up_date' => $_POST['follow_up_date'] ?? ''
];

// Validate
if (empty($data['visit_type'])) {
    jsonResponse("error", "Visit type is required");
}

if (!empty($data['visit_date'])) {
    $d = DateTime::createFromFormat('Y-m-d', $data['visit_date']);
    if (!$d || $d->format('Y-m-d') !== $data['visit_date']) {
        jsonResponse("error", "Invalid visit date format");
    }
}

if (!empty($data['follow_up_date'])) {
    $d = DateTime::createFromFormat('Y-m-d', $data['follow_up_date']);
    if (!$d || $d->format('Y-m-d') !== $data['follow_up_date']) {
        jsonResponse("error", "Invalid follow-up date format");
    }
}

try {
    $result = addMedicalRecord($pdo, $data);

    $resultData = json_decode($result, true);

    if ($resultData && $resultData['status'] === "success") {
        unset($_SESSION['csrf_token']);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        jsonResponse("success", "Medical record added successfully", [
            "csrf_token" => $_SESSION['csrf_token'],
        ]);
    } else {
        jsonResponse("error", $resultData['message'] ?? "Failed to add mdical record");
    }
} catch (Exception $e) {
    error_log("Update record failed: " . $e->getMessage());
    jsonResponse("error", "An error occurred while adding medical record");
}

ob_end_flush();