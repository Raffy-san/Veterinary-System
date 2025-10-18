<?php
include_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/session.php';
require_once __DIR__ . '/../helpers/fetch.php';
SessionManager::requireLogin();

// Require role OWNER
SessionManager::requireRole('owner');

$client = SessionManager::getUser($pdo);

if (!$client) {
    SessionManager::logout('../login.php'); // Force logout if user not found
}

$pet = fetchAllData(
    $pdo,
    "SELECT * FROM pets WHERE owner_id = ?",
    [$client['owner_id']]
);
$petCount = fetchOneData(
    $pdo,
    "SELECT COUNT(*) as total FROM pets WHERE owner_id = ?",
    [$client['owner_id']]
);

$lastVisit = fetchOneData(
    $pdo,
    "SELECT MAX(visit_date) as last_visit FROM medical_records WHERE pet_id IN (SELECT id FROM pets WHERE owner_id = ?)",
    [$client['owner_id']]
);

$medicalRecordCount = fetchOneData(
    $pdo,
    "SELECT COUNT(*) as total FROM medical_records WHERE pet_id IN (SELECT id FROM pets WHERE owner_id = ?)",
    [$client['owner_id']]
);

$speciesIcons = [
    'Dog' => ['icon' => 'fa-dog', 'bg' => 'bg-orange-100', 'color' => 'text-orange-600'],
    'Cat' => ['icon' => 'fa-cat', 'bg' => 'bg-purple-100', 'color' => 'text-purple-600'],
    'Bird' => ['icon' => 'fa-dove', 'bg' => 'bg-blue-100', 'color' => 'text-blue-600'],
    'Rabbit' => ['icon' => 'fa-carrot', 'bg' => 'bg-pink-100', 'color' => 'text-pink-600'],
    'Fish' => ['icon' => 'fa-fish', 'bg' => 'bg-cyan-100', 'color' => 'text-cyan-600'],
    'default' => ['icon' => 'fa-paw', 'bg' => 'bg-gray-100', 'color' => 'text-gray-600']
];

$speciesCount = [];
foreach ($pet as $p) {
    $species = ucfirst(strtolower($p['species']));
    $speciesCount[$species] = ($speciesCount[$species] ?? 0) + 1;
}

$Uppercase = ucfirst($client['status']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/logo.webp">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/output.css">
    <title>Owner Dashboard</title>
</head>
<style>
    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    /* Always hide FA icons that have the hidden utility */
    .fas.hidden,
    .far.hidden,
    .fal.hidden,
    .fab.hidden,
    .fa.hidden {
        display: none !important;
    }

    /* Restore responsive Tailwind display for FA icons (sm = min-width:640px) */
    @media (min-width: 640px) {

        .fas.sm\:inline,
        .far.sm\:inline,
        .fal.sm\:inline,
        .fab.sm\:inline,
        .fa.sm\:inline {
            display: inline !important;
        }

        .fas.sm\:inline-block,
        .far.sm\:inline-block,
        .fal.sm\:inline-block,
        .fab.sm\:inline-block,
        .fa.sm\:inline-block {
            display: inline-block !important;
        }

        .fas.sm\:block,
        .far.sm\:block,
        .fal.sm\:block,
        .fab.sm\:block,
        .fa.sm\:block {
            display: block !important;
        }
    }

    /* Mobile responsive adjustments */
    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .main-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<body class="bg-green-100 min-h-screen">
    <?php include_once '../includes/owner-header.php'; ?>

    <main class="p-4 md:p-10 max-w-[1400px] mx-auto">
        <!-- Stats Cards -->
        <section class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-10">
            <!-- Total Pets Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 card-hover">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="flex items-center space-x-2 md:space-x-3">
                        <div
                            class="w-10 h-10 md:w-12 md:h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-paw text-emerald-600 text-lg md:text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm md:text-base">Total Pets</h3>
                            <p class="text-xs md:text-sm text-gray-500">Registered</p>
                        </div>
                    </div>
                </div>
                <div class="text-2xl md:text-3xl font-bold text-gray-800 mb-1"><?= $petCount['total']; ?></div>
                <div class="flex items-center text-xs md:text-sm text-emerald-600">
                    <i class="fa-solid fa-arrow-up mr-1"></i>
                    <span>All pets registered</span>
                </div>
            </div>

            <!-- Medical Records Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 card-hover">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="flex items-center space-x-2 md:space-x-3">
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-file-medical text-blue-600 text-lg md:text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm md:text-base">Medical Records</h3>
                            <p class="text-xs md:text-sm text-gray-500">Total records</p>
                        </div>
                    </div>
                </div>
                <div class="text-2xl md:text-3xl font-bold text-gray-800 mb-1"><?= $medicalRecordCount['total']; ?>
                </div>
                <div class="flex items-center text-xs md:text-sm text-blue-600">
                    <i class="fa-solid fa-heart mr-1"></i>
                    <span>Health tracked</span>
                </div>
            </div>

            <!-- Account Status Card -->
            <div
                class="bg-green-200 rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 card-hover sm:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="flex items-center space-x-2 md:space-x-3">
                        <div
                            class="w-10 h-10 md:w-12 md:h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-user-check text-emerald-600 text-lg md:text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm md:text-base">Account Status</h3>
                            <p class="text-xs md:text-sm text-gray-500">Your account info</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-2 md:space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs md:text-sm text-gray-600">Account Type:</span>
                        <span
                            class="text-xs md:text-sm font-medium text-emerald-600 bg-emerald-100 px-2 py-1 rounded-full">
                            <?= htmlspecialchars($Uppercase) ?> Owner
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs md:text-sm text-gray-600">Member Since:</span>
                        <span class="text-xs md:text-sm font-medium text-gray-800">
                            <?= date('Y', strtotime($client['created_at'])) ?>
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Information Section -->
        <section class="main-grid grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
            <!-- Contact Info Card -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
                <div class="mb-4 md:mb-6">
                    <div class="flex items-center space-x-2 md:space-x-3 mb-2">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-address-card text-amber-600 text-sm md:text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-lg md:text-xl font-semibold text-gray-800">Contact Information</h3>
                            <p class="text-gray-500 text-xs md:text-sm">Information we have on file</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 md:space-y-4">
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div
                            class="w-8 h-8 md:w-10 md:h-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-3 md:mr-4">
                            <i class="fa-solid fa-envelope text-emerald-600 text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs md:text-sm text-gray-500">Email Address</p>
                            <p class="font-medium text-gray-800 text-sm md:text-base truncate">
                                <?= htmlspecialchars($client['email']); ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div
                            class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 md:mr-4">
                            <i class="fa-solid fa-phone text-blue-600 text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs md:text-sm text-gray-500">Phone Number</p>
                            <p class="font-medium text-gray-800 text-sm md:text-base">
                                <?= htmlspecialchars($client['phone']); ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div
                            class="w-8 h-8 md:w-10 md:h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3 md:mr-4">
                            <i class="fa-solid fa-map-marker-alt text-red-600 text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs md:text-sm text-gray-500">Address</p>
                            <p class="font-medium text-gray-800 text-sm md:text-base">
                                <?= htmlspecialchars($client['address']); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pet Summary Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
                <div class="mb-4">
                    <div class="flex items-center space-x-2 md:space-x-3 mb-2">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-heart text-indigo-600 text-sm md:text-base"></i>
                        </div>
                        <h3 class="text-base md:text-lg font-semibold text-gray-800">Pet Summary</h3>
                    </div>
                </div>

                <div class="space-y-3 md:space-y-4">
                    <?php foreach ($speciesCount as $species => $count):
                        $iconData = $speciesIcons[$species] ?? $speciesIcons['default'];
                        ?>
                        <div class="flex items-center justify-between p-2 md:p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-2 md:space-x-3">
                                <div
                                    class="w-6 h-6 md:w-8 md:h-8 <?= $iconData['bg']; ?> rounded-full flex items-center justify-center">
                                    <i
                                        class="fa-solid <?= $iconData['icon']; ?> <?= $iconData['color']; ?> text-xs md:text-sm"></i>
                                </div>
                                <span
                                    class="text-xs md:text-sm font-medium text-gray-800"><?= htmlspecialchars($species); ?></span>
                            </div>
                            <span class="text-xs md:text-sm font-bold text-gray-800"><?= $count; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-3 md:mt-4 pt-3 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Last vet visit</p>
                        <p class="text-xs md:text-sm font-medium text-gray-800">
                            <?= $lastVisit['last_visit'] ? date('M j, Y', strtotime($lastVisit['last_visit'])) : 'No visits yet'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>

</html>