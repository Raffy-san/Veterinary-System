<?php
require_once 'config/config.php';
require_once 'functions/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, username, password, access_type FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['access_type'] = $user['access_type'];

        echo json_encode([
            'success' => true,
            'access_type' => $user['access_type']  // send actual DB role
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid username or password'
        ]);
    }
}
