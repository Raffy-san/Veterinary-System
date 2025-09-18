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
    <link rel="stylesheet" href="../assets/css/output.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <title>My Pets</title>
</head>
<style>
    /* Mobile specific adjustments */
    @media (max-width: 640px) {
        .pet-info-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .pet-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .species-badge {
            align-self: flex-start;
        }
    }

    @media (min-width: 641px) and (max-width: 768px) {
        .pet-info-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<body class="bg-green-100">
    <?php
    include_once '../includes/owner-header.php';
    ?>
    <main class="max-w-[1400px] mx-auto px-4 md:px-8 lg:px-16 py-8">
        <div class="space-y-4 md:space-y-6">
            <?php
            $pets = fetchAllData(
                $pdo,
                "SELECT * FROM pets WHERE owner_id = ?",
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

            if ($pets) {
                foreach ($pets as $pet) {
                    $species = $pet['species'] ?? 'default';
                    $speciesInfo = $speciesData[$species] ?? $speciesData['default'];
                    ?>
                    <div class="bg-white border border-gray-200 rounded-lg p-4 md:p-8">
                        <div class="pet-header flex justify-between items-start mb-4 md:mb-6">
                            <div class="flex items-center w-full md:w-auto">
                                <div class="<?= $speciesInfo['bg'] ?> rounded-full p-2 md:p-3 mr-3 md:mr-4 flex-shrink-0">
                                    <i
                                        class="fas <?= $speciesInfo['icon'] ?> <?= $speciesInfo['color'] ?> text-lg md:text-xl"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h2 class="text-lg md:text-xl font-medium text-gray-900 mb-1 truncate">
                                        <?= htmlspecialchars($pet['name']); ?>
                                    </h2>
                                    <p class="text-sm md:text-base text-gray-500">
                                        <?= htmlspecialchars($pet['breed']); ?> •
                                        <?= htmlspecialchars($pet['age']); ?> years
                                    </p>
                                </div>
                            </div>
                            <span
                                class="species-badge <?= $speciesInfo['bg'] ?> <?= $speciesInfo['color'] ?> px-2 md:px-3 py-1 rounded text-xs md:text-sm font-medium whitespace-nowrap">
                                <?= htmlspecialchars($pet['species']); ?>
                            </span>
                        </div>

                        <div class="pet-info-grid grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6 mb-4 md:mb-6">
                            <div class="flex items-start md:items-center">
                                <i
                                    class="fas fa-venus-mars text-blue-500 mr-2 md:mr-3 text-sm md:text-base mt-1 md:mt-0 flex-shrink-0"></i>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs md:text-sm text-gray-500 mb-1">Gender</p>
                                    <p class="text-sm md:text-base text-gray-900"><?= htmlspecialchars($pet['gender']); ?></p>
                                </div>
                            </div>
                            <div class="flex items-start md:items-center">
                                <i
                                    class="fas fa-weight text-green-500 mr-2 md:mr-3 text-sm md:text-base mt-1 md:mt-0 flex-shrink-0"></i>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs md:text-sm text-gray-500 mb-1">Weight</p>
                                    <p class="text-sm md:text-base text-gray-900"><?= htmlspecialchars($pet['weight']); ?></p>
                                </div>
                            </div>
                            <div class="flex items-start md:items-center">
                                <i
                                    class="fas fa-palette text-purple-500 mr-2 md:mr-3 text-sm md:text-base mt-1 md:mt-0 flex-shrink-0"></i>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs md:text-sm text-gray-500 mb-1">Color</p>
                                    <p class="text-sm md:text-base text-gray-900"><?= htmlspecialchars($pet['color']); ?></p>
                                </div>
                            </div>
                            <div class="flex items-start md:items-center">
                                <i
                                    class="fas fa-calendar-plus text-orange-500 mr-2 md:mr-3 text-sm md:text-base mt-1 md:mt-0 flex-shrink-0"></i>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs md:text-sm text-gray-500 mb-1">Registered</p>
                                    <p class="text-sm md:text-base text-gray-900">
                                        <?= date('M d, Y', strtotime($pet['registered_at'])); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($pet['notes'])): ?>
                            <div class="border-t border-gray-100 pt-4 md:pt-6">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-sticky-note text-indigo-500 mr-2 text-sm md:text-base"></i>
                                    <p class="text-xs md:text-sm text-gray-500 font-medium">Notes</p>
                                </div>
                                <p class="text-sm md:text-base text-gray-700 leading-relaxed">
                                    <?= nl2br(htmlspecialchars($pet['notes'])); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="text-center py-12 md:py-16">
                    <i class="fas fa-paw text-gray-400 text-3xl md:text-4xl mb-4"></i>
                    <p class="text-gray-500 text-base md:text-lg">No pets found</p>
                </div>
                <?php
            }
            ?>
        </div>
    </main>
</body>

</html>