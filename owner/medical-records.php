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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Medical Records</title>
    <style>
        .record-card {
            transition: all 0.3s ease;
        }

        .record-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .record-details {
                flex-direction: column;
            }

            .detail-section {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body class="w-full bg-green-100">
    <?php include_once '../includes/owner-header.php'; ?>

    <main class="max-w-[1400px] mx-auto px-4 md:px-6 lg:px-8 py-4 md:py-8">
        <section class="w-full bg-white rounded-2xl shadow-lg p-4 md:p-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fa-solid fa-file-medical text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                            Medical Records
                        </h3>
                        <p class="text-gray-500 text-sm md:text-base">Your pets' complete medical history</p>
                    </div>
                </div>

                <!-- Stats Badge -->
                <div
                    class="inline-flex items-center bg-blue-50 text-blue-700 px-4 py-2 rounded-full text-sm font-medium">
                    <i class="fas fa-chart-line mr-2"></i>
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
                        <div class="record-card bg-white border border-gray-200 rounded-xl overflow-hidden shadow-md">
                            <!-- Pet Header -->
                            <div class="bg-gradient-to-r from-blue-500 to-purple-500 p-4 text-white">
                                <div class="flex items-center">
                                    <div class="<?= $speciesInfo['bg'] ?> rounded-full p-3 mr-4">
                                        <i class="fas <?= $speciesInfo['icon'] ?> <?= $speciesInfo['color'] ?> text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-bold"><?= htmlspecialchars($record['pet_name']) ?></h4>
                                        <p class="text-blue-100 flex items-center">
                                            <i class="fas fa-calendar mr-2"></i>
                                            <?= date('F j, Y', strtotime($record['visit_date'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Record Details -->
                            <div class="p-6">
                                <div class="record-details flex flex-col md:flex-row gap-6">
                                    <!-- Left Column -->
                                    <div class="detail-section flex-1 space-y-4">
                                        <div class="flex items-center p-3 bg-green-50 rounded-lg">
                                            <i class="fas fa-stethoscope text-green-600 mr-3"></i>
                                            <div>
                                                <p class="text-sm text-green-600 font-semibold">Visit Type</p>
                                                <p class="text-gray-900 font-medium">
                                                    <?= htmlspecialchars($record['visit_type']) ?></p>
                                            </div>
                                        </div>

                                        <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                            <i class="fas fa-weight text-blue-600 mr-3"></i>
                                            <div>
                                                <p class="text-sm text-blue-600 font-semibold">Weight</p>
                                                <p class="text-gray-900 font-medium"><?= htmlspecialchars($record['weight']) ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-center p-3 bg-red-50 rounded-lg">
                                            <i class="fas fa-thermometer-half text-red-600 mr-3"></i>
                                            <div>
                                                <p class="text-sm text-red-600 font-semibold">Temperature</p>
                                                <p class="text-gray-900 font-medium">
                                                    <?= htmlspecialchars($record['temperature']) ?></p>
                                            </div>
                                        </div>

                                        <div class="flex items-start p-3 bg-yellow-50 rounded-lg">
                                            <i class="fas fa-diagnoses text-yellow-600 mr-3 mt-1"></i>
                                            <div class="flex-1">
                                                <p class="text-sm text-yellow-600 font-semibold">Diagnosis</p>
                                                <p class="text-gray-900 font-medium leading-relaxed">
                                                    <?= htmlspecialchars($record['diagnosis']) ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="detail-section flex-1 space-y-4">
                                        <div class="flex items-start p-3 bg-purple-50 rounded-lg">
                                            <i class="fas fa-user-md text-purple-600 mr-3 mt-1"></i>
                                            <div class="flex-1">
                                                <p class="text-sm text-purple-600 font-semibold">Treatment</p>
                                                <p class="text-gray-900 font-medium leading-relaxed">
                                                    <?= htmlspecialchars($record['treatment']) ?></p>
                                            </div>
                                        </div>

                                        <div class="flex items-start p-3 bg-indigo-50 rounded-lg">
                                            <i class="fas fa-pills text-indigo-600 mr-3 mt-1"></i>
                                            <div class="flex-1">
                                                <p class="text-sm text-indigo-600 font-semibold">Medications</p>
                                                <p class="text-gray-900 font-medium leading-relaxed">
                                                    <?= htmlspecialchars($record['medications']) ?></p>
                                            </div>
                                        </div>

                                        <?php if (!empty($record['follow_up_date'])): ?>
                                            <div class="flex items-center p-3 bg-orange-50 rounded-lg">
                                                <i class="fas fa-calendar-check text-orange-600 mr-3"></i>
                                                <div>
                                                    <p class="text-sm text-orange-600 font-semibold">Follow-up Date</p>
                                                    <p class="text-gray-900 font-medium">
                                                        <?= date('M j, Y', strtotime($record['follow_up_date'])) ?></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($record['notes'])): ?>
                                            <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                                                <i class="fas fa-sticky-note text-gray-600 mr-3 mt-1"></i>
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-600 font-semibold">Additional Notes</p>
                                                    <p class="text-gray-900 font-medium leading-relaxed">
                                                        <?= nl2br(htmlspecialchars($record['notes'])) ?></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="text-center py-16">
                        <div class="bg-gray-50 rounded-2xl p-12 max-w-md mx-auto">
                            <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-file-medical text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4">No Medical Records</h3>
                            <p class="text-gray-500">No medical records have been added for your pets yet. Medical records
                                will appear here once they are created by veterinarians.</p>
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