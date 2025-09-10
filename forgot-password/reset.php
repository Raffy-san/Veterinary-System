<?php
require_once "../config/config.php";

// Always set timezone
date_default_timezone_set('Asia/Manila');

if (!isset($_GET['token'])) {
    die("Invalid or expired reset link.");
}

$token = $_GET['token'];

// Look up the token in DB
$stmt = $pdo->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("Invalid or expired reset link. (No user found)");
}

// Debugging (you can remove later)
echo "<pre>";
echo "Token from URL: $token\n";
echo "Token expiry from DB: " . $user['reset_expires'] . "\n";
echo "Current PHP time: " . date("Y-m-d H:i:s") . "\n";
echo "</pre>";

// Check expiration
if (strtotime($user['reset_expires']) < time()) {
    die("Invalid or expired reset link. (Token expired)");
}

// If form submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Update password and clear token
    $stmt = $pdo->prepare("UPDATE users 
                           SET password = ?, reset_token = NULL, reset_expires = NULL 
                           WHERE id = ?");
    $stmt->execute([$newPassword, $user['id']]);

    echo "Password has been reset successfully!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>

<body>
    <h2>Set New Password</h2>
    <form method="POST">
        <input type="password" name="password" placeholder="Enter new password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>

</html>