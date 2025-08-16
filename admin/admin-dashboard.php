<?php
include_once '../config/config.php';
require_once '../session.php';
SessionManager::requireLogin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <h4 class="font-semibold">4</h4>
                <h4 class="text-gray-500">Registered Clients</h4>
            </div>
            <div class="bg-white rounded-lg shadow-md p-8 flex-1">
                <div class="w-full flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Active Clients</h3>
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <h4 class="font-semibold">0</h4>
                <h4 class="text-gray-500">Active Clients</h4>
            </div>
            <div class="bg-white rounded-lg shadow-md p-8 flex-1">
                <div class="w-full flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Total Pets</h3>
                    <i class="fa-solid fa-paw"></i>
                </div>
                <h4 class="font-semibold">3</h4>
                <h4 class="text-gray-500">Registered Pets</h4>
            </div>
        </section>
        <section class="px-10 flex flex-col md:flex-row gap-8 w-full mb-6">
            <div class="bg-white rounded-lg shadow-md p-8 flex-1">
                <div class="mb-4">
                    <h3 class="font-semibold">Recent Client Activity</h3>
                    <h4 class="text-gray-500">Latest Client Registration</h4>
                </div>
                <div class="flex items-center border rounded-lg p-4 mb-4">
                    <i class="fa-solid fa-user-plus mr-4"></i>
                    <div>
                        <h4 class="font-semibold">Client Registered: John Doe</h4>
                        <h4 class="text-gray-500">Registered on 2023-03-15</h4>
                    </div>
                </div>
                <div class="flex items-center border rounded-lg p-4 mb-4">
                    <i class="fa-solid fa-user-plus mr-4"></i>
                    <div>
                        <h4 class="font-semibold">Client Registered: John Doe</h4>
                        <h4 class="text-gray-500">Registered on 2023-03-15</h4>
                    </div>
                </div>
                <div class="flex items-center border rounded-lg p-4 mb-4">
                    <i class="fa-solid fa-user-plus mr-4"></i>
                    <div>
                        <h4 class="font-semibold">Client Registered: John Doe</h4>
                        <h4 class="text-gray-500">Registered on 2023-03-15</h4>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-8 flex-1">
                <div class="mb-4">
                    <h4 class="font-semibold">System Status</h4>
                    <h4 class="text-gray-500">Current Clinic Operation</h4>
                </div>
            </div>
        </section>
    </main>
</body>

</html>