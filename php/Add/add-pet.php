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
    'owner_id' => filter_input(INPUT_POST, 'owner_id', FILTER_VALIDATE_INT),
    'name' => ucwords(strtolower(trim($_POST['name'] ?? ''))),
    'species' => trim($_POST['species'] ?? ''),
    'breed' => trim($_POST['breed'] ?? ''),
    'gender' => trim($_POST['gender'] ?? ''),
    'age' => ($_POST['age'] !== '' ? filter_var($_POST['age'], FILTER_VALIDATE_INT) : null),
    'weight' => trim($_POST['weight'] ?? ''),
    'color' => trim($_POST['color'] ?? ''),
    'notes' => trim($_POST['notes'] ?? '')
];

if ($data['owner_id'] === false || $data['owner_id'] === null) {
    jsonResponse("error", "Invalid owner ID");
}

try {
    $result = addPet($pdo, $data);

    $resultData = json_decode($result, true);

    if ($resultData && $resultData['status'] === "success") {
        unset($_SESSION['csrf_token']);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        jsonResponse("success", "Pet added successfully", [
            "csrf_token" => $_SESSION['csrf_token'],
        ]);
    } else {
        jsonResponse("error", $resultData['message'] ?? "Failed to add pet");
    }
} catch (Exception $e) {
    error_log("Add pet failed: " . $e->getMessage());
    jsonResponse("error", "An error occurred while adding pet");
}

ob_end_flush();