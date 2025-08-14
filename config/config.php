<?php
// Use environment variables for sensitive data
$localhost = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'veterinary_system';

// Use mysqli with error reporting and exception mode
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($localhost, $username, $password, $dbname);
    $conn->set_charset('utf8mb4'); // Set charset for security
} catch (mysqli_sql_exception $e) {
    error_log('Database connection error: ' . $e->getMessage());
    // Show generic error message to user
    die('Database connection failed. Please try again later.');
}
?>