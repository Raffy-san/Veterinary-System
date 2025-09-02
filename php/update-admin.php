<?php
include_once '../config/config.php';
require_once '../functions/session.php';
require_once '../functions/crud.php';

SessionManager::requireLogin();
SessionManager::requireRole('admin');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'admin_id' => $_POST['admin_id'] ?? '',
        'username' => $_POST['username'] ?? '',
        'password' => $_POST['password'] ?? ''
    ];

    echo updateAdmin($pdo, $data);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
