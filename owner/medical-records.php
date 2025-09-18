<?php
require_once '../functions/session.php';
include_once '../config/config.php';
require_once '../helpers/fetch.php';
SessionManager::requireLogin();
// Require role OWNER
SessionManager::requireRole('owner');

$client = SessionManager::getUser($pdo);

if (!$client) {
    SessionManager::logout('../login.php'); // Force logout if user not found
}

$medicalRecordCount = fetchOneData(
    $pdo,
    "SELECT COUNT(*) as total FROM medical_records WHERE pet_id IN (SELECT id FROM pets WHERE owner_id = ?)",
    [$client['owner_id']]
);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/green-paw.png">
    <link rel="stylesheet" href="../assets/css/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Medical Records</title>
</head>
<style>
    .record-card {
        transition: all 0.3s ease;
    }

    .record-card:hover {
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
</style>

<body class="w-full bg-green-100">
    <?php include_once '../includes/owner-header.php'; ?>

    <main class="max-w-[1400px] mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-4 md:py-8">
        <section class="w-full bg-white rounded-2xl shadow-lg p-4 sm:p-6 md:p-8">
            <!-- Header Section -->
            <div class="mb-6 sm:mb-8">
                <div class="flex items-center mb-3 sm:mb-4">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                        <i class="fa-solid fa-file-medical text-blue-600 text-lg sm:text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 flex items-center">
                            Medical Records
                        </h3>
                        <p class="text-gray-500 text-xs sm:text-sm md:text-base">Your pets' complete medical history</p>
                    </div>
                </div>

                <!-- Stats Badge -->
                <div
                    class="inline-flex items-center bg-blue-50 text-blue-700 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium">
                    <i class="fas fa-chart-line mr-1.5 sm:mr-2"></i>
                    <?= $medicalRecordCount['total'] ?> Total Records
                </div>
            </div>

            <!-- Medical Records List -->
            <div class="space-y-6">
                <?php
                $medicalRecord = fetchAllData(
                    $pdo,
                    "SELECT medical_records.*, pets.name AS pet_name, pets.species
                            FROM medical_records
                            INNER JOIN pets ON medical_records.pet_id = pets.id
                            WHERE pets.owner_id = ?
                            ORDER BY visit_date DESC",
                    [$client['owner_id']]
                );

                // Define species icons and colors
                $speciesData = [
                    'Dog' => ['icon' => 'fa-dog', 'color' => 'text-blue-600', 'bg' => 'bg-blue-100'],
                    'Cat' => ['icon' => 'fa-cat', 'color' => 'text-purple-600', 'bg' => 'bg-purple-100'],
                    'Bird' => ['icon' => 'fa-dove', 'color' => 'text-green-600', 'bg' => 'bg-green-100'],
                    'Fish' => ['icon' => 'fa-fish', 'color' => 'text-teal-600', 'bg' => 'bg-teal-100'],
                    'Rabbit' => ['icon' => 'fa-rabbit', 'color' => 'text-pink-600', 'bg' => 'bg-pink-100'],
                    'Hamster' => ['icon' => 'fa-mouse', 'color' => 'text-orange-600', 'bg' => 'bg-orange-100'],
                    'default' => ['icon' => 'fa-paw', 'color' => 'text-gray-600', 'bg' => 'bg-gray-100']
                ];

                if ($medicalRecord) {
                    foreach ($medicalRecord as $record) {
                        $species = $record['species'] ?? 'default';
                        $speciesInfo = $speciesData[$species] ?? $speciesData['default'];
                        ?>
                        <div class="record-card bg-white border border-gray-200 rounded-xl overflow-hidden shadow-md mb-6">
                            <!-- Pet Header -->
                            <div class="bg-gradient-to-r from-blue-500 to-purple-500 p-3 sm:p-4 text-white">
                                <div class="flex items-center">
                                    <div class="<?= $speciesInfo['bg'] ?> rounded-full p-2 sm:p-3 mr-3 sm:mr-4">
                                        <i
                                            class="fas <?= $speciesInfo['icon'] ?> <?= $speciesInfo['color'] ?> text-sm sm:text-base md:text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-base sm:text-lg md:text-xl font-bold">
                                            <?= htmlspecialchars($record['pet_name']) ?>
                                        </h4>
                                        <p class="text-blue-100 text-xs sm:text-sm flex items-center">
                                            <i class="fas fa-calendar mr-1 sm:mr-2"></i>
                                            <?= date('F j, Y', strtotime($record['visit_date'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Record Details -->
                            <div class="p-4 sm:p-6">
                                <div class="record-details flex flex-col md:flex-row gap-4 sm:gap-6">
                                    <!-- Left Column -->
                                    <div class="detail-section w-full md:w-1/2 space-y-3 sm:space-y-4">
                                        <div class="flex items-center p-2 sm:p-3 bg-green-50 rounded-lg">
                                            <i class="fas fa-stethoscope text-green-600 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                            <div>
                                                <p class="text-xs sm:text-sm text-green-600 font-semibold">Visit Type</p>
                                                <p class="text-gray-900 font-medium text-sm sm:text-base">
                                                    <?= htmlspecialchars($record['visit_type']) ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start p-2 sm:p-3 bg-purple-50 rounded-lg">
                                            <i
                                                class="fas fa-user-md text-purple-600 mr-2 sm:mr-3 mt-0.5 sm:mt-1 text-sm sm:text-base"></i>
                                            <div class="flex-1">
                                                <p class="text-xs sm:text-sm text-purple-600 font-semibold">Veterinarian</p>
                                                <p class="text-gray-900 font-medium leading-relaxed text-sm sm:text-base">
                                                    <?= htmlspecialchars($record['veterinarian']) ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex flex-row sm:flex-col gap-3 sm:gap-4">
                                            <div class="flex items-center p-2 sm:p-3 bg-blue-50 rounded-lg flex-1">
                                                <i class="fas fa-weight text-blue-600 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                                <div>
                                                    <p class="text-xs sm:text-sm text-blue-600 font-semibold">Weight</p>
                                                    <p class="text-gray-900 font-medium text-sm sm:text-base">
                                                        <?= htmlspecialchars($record['weight']) ?>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-center p-2 sm:p-3 bg-red-50 rounded-lg flex-1">
                                                <i
                                                    class="fas fa-thermometer-half text-red-600 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                                <div>
                                                    <p class="text-xs sm:text-sm text-red-600 font-semibold">Temperature</p>
                                                    <p class="text-gray-900 font-medium text-sm sm:text-base">
                                                        <?= htmlspecialchars($record['temperature']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="detail-section w-full md:w-1/2 space-y-3 sm:space-y-4">
                                        <div class="flex items-start p-2 sm:p-3 bg-purple-50 rounded-lg">
                                            <i
                                                class="fas fa-medkit text-purple-600 mr-2 sm:mr-3 mt-0.5 sm:mt-1 text-sm sm:text-base"></i>
                                            <div class="flex-1">
                                                <p class="text-xs sm:text-sm text-purple-600 font-semibold">Treatment</p>
                                                <p class="text-gray-900 font-medium leading-relaxed text-sm sm:text-base">
                                                    <?= htmlspecialchars($record['treatment']) ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start p-2 sm:p-3 bg-indigo-50 rounded-lg">
                                            <i
                                                class="fas fa-pills text-indigo-600 mr-2 sm:mr-3 mt-0.5 sm:mt-1 text-sm sm:text-base"></i>
                                            <div class="flex-1">
                                                <p class="text-xs sm:text-sm text-indigo-600 font-semibold">Medications</p>
                                                <p class="text-gray-900 font-medium leading-relaxed text-sm sm:text-base">
                                                    <?= htmlspecialchars($record['medications']) ?>
                                                </p>
                                            </div>
                                        </div>

                                        <?php if (!empty($record['follow_up_date'])): ?>
                                            <div class="flex items-center p-2 sm:p-3 bg-orange-50 rounded-lg">
                                                <i
                                                    class="fas fa-calendar-check text-orange-600 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                                <div>
                                                    <p class="text-xs sm:text-sm text-orange-600 font-semibold">Follow-up Date</p>
                                                    <p class="text-gray-900 font-medium text-sm sm:text-base">
                                                        <?= date('M j, Y', strtotime($record['follow_up_date'])) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($record['notes'])): ?>
                                            <div class="flex items-start p-2 sm:p-3 bg-gray-50 rounded-lg">
                                                <i
                                                    class="fas fa-sticky-note text-gray-600 mr-2 sm:mr-3 mt-0.5 sm:mt-1 text-sm sm:text-base"></i>
                                                <div class="flex-1">
                                                    <p class="text-xs sm:text-sm text-gray-600 font-semibold">Additional Notes</p>
                                                    <p class="text-gray-900 font-medium leading-relaxed text-sm sm:text-base">
                                                        <?= nl2br(htmlspecialchars($record['notes'])) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                } else {
                    ?>
                <div class="text-center py-12 sm:py-16">
                    <div class="bg-gray-50 rounded-2xl p-8 sm:p-12 max-w-md mx-auto">
                        <div
                            class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                            <i class="fas fa-file-medical text-gray-400 text-2xl sm:text-3xl"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 sm:mb-4">No Medical Records</h3>
                        <p class="text-gray-500 text-sm sm:text-base">No medical records have been added for your pets
                            yet. Medical records will appear here once they are created by veterinarians.</p>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
        </section>
    </main>
</body>

</html>