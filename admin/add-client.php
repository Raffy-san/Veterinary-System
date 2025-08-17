<?php
include_once '../config/config.php';
require_once '../functions/session.php';
SessionManager::requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $emergency_contact = $_POST['emergency_contact'];
    $address = $_POST['address'];

    // 1. Insert into users table
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $access_type = 'owner'; // or whatever value you use for pet owners
    $active = 1; // Set to 1 for active, 0 for inactive

    $stmt = $pdo->prepare("INSERT INTO users (username, password, access_type, active) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $hashedPassword, $access_type, $active]);
    $user_id = $pdo->lastInsertId();

    // 2. Insert into owners table, referencing the user_id
    $stmt = $pdo->prepare("INSERT INTO owners (user_id, name, email, phone, emergency, address, active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $name, $email, $phone, $emergency_contact, $address, $active]);

    // Redirect or show a success message
    header('Location: client-management.php');
    exit;
}

?>