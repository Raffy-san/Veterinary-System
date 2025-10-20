<?php
include_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/session.php';
require_once __DIR__ . '/../helpers/fetch.php';
SessionManager::requireLogin();
SessionManager::requireRole('admin');

$admin = SessionManager::getUser($pdo);

if (!$admin) {
    SessionManager::logout('../login.php');
}

$petOwners = fetchAllData($pdo, "SELECT * FROM users WHERE access_type = 'owner'");
$allPetOwners = fetchAllData($pdo, "SELECT * FROM owners ORDER BY created_at DESC LIMIT 4");
$activePetOwners = fetchAllData($pdo, "SELECT * FROM owners WHERE status = 1");
$totalPets = fetchAllData($pdo, "SELECT * FROM pets");
$totalRecords = fetchOneData($pdo, "SELECT COUNT(*) AS total_transactions_today
FROM medical_records
WHERE is_deleted = 0 AND visit_date = CURDATE()");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/logo.webp">
    <link rel="stylesheet" href="../assets/css/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .card-hover {
            transition: box-shadow 0.2s ease;
        }

        .card-hover:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
    <title>Veterinary Clinic - Dashboard Overview</title>
</head>

<body class="bg-green-100 w-full min-h-screen overflow-y-auto">
    <?php
    include_once '../includes/admin-header.php';
    ?>
    <main class="pb-10 max-w-[1400px] mx-auto">
        <!-- Statistics Cards Section -->
        <section class="p-6 md:p-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
                <!-- Total Clients Card -->
                <div class="bg-white rounded-lg shadow-md p-6 card-hover border-l-4 border-blue-500">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700">Total Clients</h3>
                            <p class="text-sm text-gray-500">All registered clients</p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <i class="fa-solid fa-users text-xl text-blue-600"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800"><?php echo count($petOwners); ?></h2>
                        <span
                            class="bg-blue-50 text-blue-700 text-xs font-medium px-2 py-1 rounded-full mt-2 inline-block">
                            Registered
                        </span>
                    </div>
                </div>

                <!-- Active Clients Card -->
                <div class="bg-white rounded-lg shadow-md p-6 card-hover border-l-4 border-green-600">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700">Total Visits</h3>
                            <p class="text-sm text-gray-500">Todays total Vet Visits</p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <i class="fa-solid fa-user-check text-xl text-green-600"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">
                            <?php echo $totalRecords['total_transactions_today']; ?>
                        </h2>
                        <span
                            class="bg-green-50 text-green-700 text-xs font-medium px-2 py-1 rounded-full mt-2 inline-block">
                            Visits
                        </span>
                    </div>
                </div>

                <!-- Total Pets Card -->
                <div class="bg-white rounded-lg shadow-md p-6 card-hover border-l-4 border-orange-500">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700">Total Pets</h3>
                            <p class="text-sm text-gray-500">Registered pets in system</p>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-lg">
                            <i class="fa-solid fa-paw text-xl text-orange-600"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800"><?php echo count($totalPets); ?></h2>
                        <span
                            class="bg-orange-50 text-orange-700 text-xs font-medium px-2 py-1 rounded-full mt-2 inline-block">
                            Pets
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Activity and Status Section -->
        <section class="px-6 md:px-10 grid grid-cols-1 lg:grid-cols-2 gap-8 w-full mb-6">
            <!-- Recent Client Activity -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover animate-slide-up" style="animation-delay: 0.3s;">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                            <i class="fa-solid fa-clock-rotate-left text-vet-purple mr-3"></i>
                            Recent Activity
                        </h3>
                        <p class="text-gray-600">Latest client registrations</p>
                    </div>
                    <div class="bg-purple-100 p-2 rounded-lg">
                        <i class="fa-solid fa-chart-line text-vet-purple"></i>
                    </div>
                </div>

                <div class="space-y-4">
                    <?php
                    $colors = ['bg-blue-50 border-blue-200', 'bg-green-50 border-green-200', 'bg-purple-50 border-purple-200'];
                    $iconColors = ['text-blue-600', 'text-green-600', 'text-purple-600'];
                    $index = 0;

                    foreach ($allPetOwners as $owner) {
                        $colorClass = $colors[$index % 3];
                        $iconColor = $iconColors[$index % 3];
                        echo '<div class="flex items-center p-4 ' . $colorClass . ' border rounded-lg hover:shadow-md transition-all duration-300">';
                        echo '<div class="bg-white p-2 rounded-full mr-4">';
                        echo '<i class="fa-solid fa-user-plus ' . $iconColor . '"></i>';
                        echo '</div>';
                        echo '<div class="flex-1">';
                        echo '<h4 class="font-semibold text-gray-800">New Client: ' . htmlspecialchars($owner['name']) . '</h4>';
                        echo '<p class="text-sm text-gray-600">Registered on ' . date('M d, Y', strtotime($owner['created_at'])) . '</p>';
                        echo '</div>';
                        echo '<div class="bg-white px-3 py-1 rounded-full">';
                        echo '<span class="text-xs font-medium text-gray-700">New</span>';
                        echo '</div>';
                        echo '</div>';
                        $index++;
                    }
                    ?>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover animate-slide-up" style="animation-delay: 0.4s;">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                            <i class="fa-solid fa-stethoscope text-vet-pink mr-3"></i>
                            System Status
                        </h3>
                        <p class="text-gray-600">Current clinic operation metrics</p>
                    </div>
                    <div class="bg-pink-100 p-2 rounded-lg">
                        <i class="fa-solid fa-hospital text-vet-pink"></i>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Active Clients Status -->
                    <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-xl border border-green-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="bg-green-500 p-2 rounded-lg mr-3">
                                    <i class="fa-solid fa-user-check text-white text-sm"></i>
                                </div>
                                <h4 class="font-semibold text-gray-800">Active Clients</h4>
                            </div>
                            <div class="bg-green-500 text-white px-4 py-2 rounded-full font-bold text-lg">
                                <?php echo count($activePetOwners); ?>
                            </div>
                        </div>
                        <div class="w-full bg-green-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full"
                                style="width: <?php echo min(100, (count($activePetOwners) / max(1, count($petOwners))) * 100); ?>%">
                            </div>
                        </div>
                        <p class="text-xs text-green-700 mt-2">
                            <?php echo round((count($activePetOwners) / max(1, count($petOwners))) * 100, 1); ?>% of
                            total clients
                        </p>
                    </div>

                    <!-- Total Pets Status -->
                    <div class="bg-gradient-to-r from-orange-50 to-orange-100 p-4 rounded-xl border border-orange-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="bg-orange-500 p-2 rounded-lg mr-3">
                                    <i class="fa-solid fa-paw text-white text-sm"></i>
                                </div>
                                <h4 class="font-semibold text-gray-800">Total Pets</h4>
                            </div>
                            <div class="bg-orange-500 text-white px-4 py-2 rounded-full font-bold text-lg">
                                <?php echo count($totalPets); ?>
                            </div>
                        </div>
                        <div class="w-full bg-orange-200 rounded-full h-2">
                            <div class="bg-orange-500 h-2 rounded-full"
                                style="width: <?php echo min(100, (count($totalPets) / max(1, count($totalPets) + 50)) * 100); ?>%">
                            </div>
                        </div>
                        <p class="text-xs text-orange-700 mt-2">Pets registered in the system</p>
                    </div>

                    <!-- System Health Indicator -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-blue-500 p-2 rounded-lg mr-3">
                                    <i class="fa-solid fa-heartbeat text-white text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">System Health</h4>
                                    <p class="text-sm text-gray-600">All systems operational</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-medium text-green-700">Online</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>

</html>