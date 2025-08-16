<?php
// Database connection settings
$host = 'localhost';
$db   = 'veterinary_system';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Create connection
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Example data (replace with your form data)
$admin_username = 'admin123';
$admin_password = password_hash('super@dmin123', PASSWORD_DEFAULT); // Always hash passwords!
$access_type = 'admin';

// Insert query
$sql = "INSERT INTO users (username, password, access_type) VALUES (:username, :password, :access_type)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':username' => $admin_username,
    ':password' => $admin_password,
    ':access_type' => $access_type
]);

echo "Admin inserted successfully.";
?>