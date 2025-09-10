<?php
require_once 'config/config.php';
require_once 'functions/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT id, email, password, access_type FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Secure session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['access_type'] = $user['access_type']; // use 'role' consistently

        echo json_encode([
            'success' => true,
            'access_type' => $user['access_type'] // send role for JS redirect
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email or password'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
