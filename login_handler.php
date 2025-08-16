<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/config.php';

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Use prepared statements for security
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // If you store hashed passwords, use password_verify
        // if (password_verify($password, $user['password'])) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['access_type'] = $user['access_type'];

            // Return JSON with access type
            echo json_encode([
                'success' => true,
                'access_type' => $user['access_type']
            ]);
            exit;
        }
    }

    // Login failed
    echo json_encode([
        'success' => false,
        'message' => 'Invalid username or password.'
    ]);
    exit;
}
echo json_encode([
    'success' => false,
    'message' => 'Invalid request.'
]);
exit;
?>