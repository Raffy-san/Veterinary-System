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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse("error", "Invalid request method");
}

if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    jsonResponse("error", "Invalid CSRF token");
}

$data = [
    'pet_id' => filter_input(INPUT_POST, 'pet_id', FILTER_VALIDATE_INT),
    'name' => trim($_POST['name'] ?? ''),
    'species' => trim($_POST['species'] ?? ''),
    'breed' => trim($_POST['breed'] ?? ''),
    'age' => ($age = filter_var($_POST['age'] ?? null, FILTER_VALIDATE_INT)) !== false ? $age : null,
    'gender' => trim($_POST['gender'] ?? ''),
    'weight' => trim($_POST['weight'] ?? ''),
    'color' => trim($_POST['color'] ?? ''),
    'notes' => trim($_POST['notes'] ?? '')
];

if (!$data['pet_id']) {
    jsonResponse("error", "Invalid or missing pet ID");
}

// Run update
$result = updatePet($pdo, $data);

// Decode updatePet result
$resultData = json_decode($result, true);

// If success, regenerate CSRF token for next requests
if ($resultData && $resultData['status'] === "success") {
    unset($_SESSION['csrf_token']);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    jsonResponse("success", "Pet account updated successfully", [
        "csrf_token" => $_SESSION['csrf_token']
    ]);
} else {
    jsonResponse("error", $resultData['message'] ?? "Failed to update pet account");
}

ob_end_flush();