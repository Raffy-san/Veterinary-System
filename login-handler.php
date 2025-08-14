<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include database configuration
    require_once 'config/config.php';

    // Get form data
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate and sanitize input
    $username = $conn->real_escape_string(trim($username));
    $password = $conn->real_escape_string(trim($password));

    // Check credentials
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows === 1) {
        // Login successful
        $_SESSION['username'] = $username;
        header('Location: dashboard.php');
        exit;
    } else {
        // Login failed
        $error = "Invalid username or password.";
    }
}
?>