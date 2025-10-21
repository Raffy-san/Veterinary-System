<?php
ini_set('display_errors', 0);
error_reporting(0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/functions/session.php';
require_once __DIR__ . '/functions/settings.php'; // ✅ make sure this file has getDefaultPassword()

header('Content-Type: application/json; charset=utf-8');

$response = [
    'success' => false,
    'message' => 'Something went wrong.'
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['message'] = 'Invalid request method';
        echo json_encode($response);
        exit;
    }

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $response['message'] = 'Email and password are required';
        echo json_encode($response);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, email, password, access_type, status FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $response['message'] = 'Invalid email or password';
        echo json_encode($response);
        exit;
    }

    if ($user['status'] !== 'Active') {
        $response['message'] = 'Your account is inactive. Please contact support.';
        echo json_encode($response);
        exit;
    }

    // ✅ Get the dynamic default password based on role
    $defaultPassword = getDefaultPassword($pdo, strtolower($user['access_type']));

    if (password_verify($password, $user['password']) || $password === $defaultPassword) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['access_type'] = $user['access_type'];

        echo json_encode([
            'success' => true,
            'access_type' => $user['access_type']
        ]);
        exit;
    } else {
        $response['message'] = 'Invalid email or password';
        echo json_encode($response);
        exit;
    }


} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    $response['message'] = 'Server error, please try again later.';
    echo json_encode($response);
    exit;
}
