<?php
require_once '../functions/session.php';
include_once '../config/config.php';
require_once '../helpers/fetch.php';
SessionManager::requireLogin();

$client = SessionManager::getUser($pdo);
$petCount = fetchAllData(
    $pdo,
    "SELECT * FROM medical_records WHERE pet_id IN (SELECT id FROM pets WHERE owner_id = ?)",
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
    <main class="p-10">
        <section class="p-10 w-full bg-white rounded-lg shadow-md">
            <div class="mb-6">
                <h3 class="font-semibold">Medical Record</h3>
                <h4 class="text-gray-500">My pets personal informations</h4>
            </div>
    </section>
</body>

</html>