<?php
include_once '../config/config.php';
require_once '../functions/session.php';
require_once '../helpers/fetch.php';
SessionManager::requireLogin();

$petOwners = fetchAllData($pdo, "SELECT * FROM users WHERE access_type = 'owner'");
$allPetOwners = fetchAllData($pdo, "SELECT * FROM owners ORDER BY created_at DESC LIMIT 3");
$activePetOwners = fetchAllData($pdo, "SELECT * FROM owners WHERE status = 1");
$totalPets = fetchAllData($pdo, "SELECT * FROM pets");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/green-paw.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Overview</title>
</head>

<body class="bg-green-100 w-full">
    <?php
    include_once '../includes/admin-header.php';
    ?>
    <main>
        <section class="p-10 flex flex-col md:flex-row gap-8 w-full">
            <div class="bg-white rounded-lg shadow-md p-8 flex-1">
                <div class="w-full flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Total Clients</h3>
                    <i class="fa-solid fa-user"></i>
                </div>
                <h4 class="font-semibold"><?php echo count($petOwners); ?></h4>
                <h4 class="text-gray-500">Registered Clients</h4>
            </div>
            <div class="bg-white rounded-lg shadow-md p-8 flex-1">
                <div class="w-full flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Active Clients</h3>
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <h4 class="font-semibold"><?php echo count($activePetOwners); ?></h4>
                <h4 class="text-gray-500">Active Clients</h4>
            </div>
            <div class="bg-white rounded-lg shadow-md p-8 flex-1">
                <div class="w-full flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Total Pets</h3>
                    <i class="fa-solid fa-paw"></i>
                </div>
                <h4 class="font-semibold"><?php echo count($totalPets); ?></h4>
                <h4 class="text-gray-500">Registered Pets</h4>
            </div>
        </section>
        <section class="px-10 flex flex-col md:flex-row gap-8 w-full mb-6">
            <div class="bg-white rounded-lg shadow-md p-8 flex-1">
                <div class="mb-4">
                    <h3 class="font-semibold">Recent Client Activity</h3>
                    <h4 class="text-gray-500">Latest Client Registration</h4>
                </div>
                <?php
                foreach ($allPetOwners as $owner) {
                    echo '<div class="flex items-center border rounded-lg p-4 mb-4">';
                    echo '<i class="fa-solid fa-user-plus mr-4"></i>';
                    echo '<div>';
                    echo '<h4 class="font-semibold">Client Registered: ' . htmlspecialchars($owner['name']) . '</h4>';
                    echo '<h4 class="text-gray-500">Registered on ' . htmlspecialchars($owner['created_at']) . '</h4>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
            <div class="bg-white rounded-lg shadow-md p-8 flex-1">
                <div class="mb-4">
                    <h4 class="font-semibold">System Status</h4>
                    <h4 class="text-gray-500">Current Clinic Operation</h4>
                </div>
                <div class="flex justify-between flex-col mb-4">
                    <div class="flex justify-between w-full mb-2">
                        <h4 class="font-semibold">Active Clients</h4>
                        <p class="py-1 px-2 text-sm font-semibold text-white border bg-green-600 rounded-lg"><?php echo count($activePetOwners); ?></p>
                    </div>
                    <div class="flex justify-between w-full mb-2">
                        <h4 class="font-semibold">Total Pets</h4>
                        <p class="py-1 px-2 text-sm font-semibold text-black border bg-green-400 rounded-lg"><?php echo count($totalPets); ?></p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>

</html>