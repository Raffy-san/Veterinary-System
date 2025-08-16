<?php 
    require_once '../session.php';
    include_once '../config/config.php';
    SessionManager::requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <?php echo "<h1>Welcome, " . $_SESSION['username'] . "!</h1>"; ?>
    <a href="../logout.php">Logout</a>
</body>
</html>