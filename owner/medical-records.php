<?php
require_once '../functions/session.php';
include_once '../config/config.php';
require_once '../helpers/fetch.php';
SessionManager::requireLogin();

$client = SessionManager::getUser($pdo);
$petCount = fetchOneData(
    $pdo,
    "SELECT COUNT(*) as total FROM pets WHERE owner_id = ?",
    [$client['owner_id']]
);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/green-paw.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Medical Records</title>
</head>

<body class="w-full bg-green-100">
    <?php
    include_once '../includes/owner-header.php';
    ?>
</body>

</html>