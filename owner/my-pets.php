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
    <title>My Pets</title>
</head>

<body class="w-full bg-green-100">
    <?php
    include_once '../includes/owner-header.php';
    ?>
    <main class="p-10">
        <section class="p-10 w-full bg-white rounded-lg shadow-md">
            <div class="mb-6">
                <h3 class="font-semibold">My Pets</h3>
                <h4 class="text-gray-500">My pets personal informations</h4>
            </div>
            <div>
                <div class="">
                    <?php
                    $pets = fetchAllData(
                        $pdo,
                        "SELECT * FROM pets WHERE owner_id = ?",
                        [$client['owner_id']]
                    );
                    if ($pets) {
                        foreach ($pets as $pet) {
                            ?>
                            <div class="bg-white rounded-lg border border-green-200 p-6 mb-4">
                                <div class="flex flex-row items-center">
                                    <i class="fa-solid fa-paw text-green-500 mr-2"></i>
                                    <h4 class="font-semibold text-lg mr-2"><?= htmlspecialchars($pet['name']); ?></h4>
                                    <h4 class="bg-green-100 rounded-lg px-2 py-1 text-green-600">
                                        <?= htmlspecialchars($pet['species']); ?>
                                    </h4>
                                </div>
                                <div class="flex flex-row items-center mb-4">
                                    <p class="text-gray-500 mr-2"><?= htmlspecialchars($pet['breed']); ?></p>
                                    <span class=" text-gray-500 font-semibold mr-2">•</span>
                                    <p class="text-gray-500"><?= htmlspecialchars($pet['age']); ?> years</p>
                                </div>
                                <div class="flex flex-row mb-4">
                                    <div class="flex flex-col mb-6 w-1/2">
                                        <p class="text-gray-500"><span class="text-black font-semibold">Gender:
                                            </span><?= htmlspecialchars($pet['gender']); ?></p>
                                        <p class="text-gray-500"><span class="text-black font-semibold">Weight:
                                            </span><?= htmlspecialchars($pet['weight']); ?></p>
                                        <p class="text-gray-500"><span class="text-black font-semibold">Color:
                                            </span><?= htmlspecialchars($pet['color']); ?></p>
                                    </div>
                                    <div class="flex flex-col mb-6 w-1/2">
                                        <p class="text-gray-500"><span class="text-black font-semibold">Registration
                                                Date: </span><?= htmlspecialchars($pet['registered_at']) ?>
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-gray-500"><span class="text-black font-semibold">Notes:<br>
                                        </span><?= htmlspecialchars($pet['notes']); ?></p>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p class='text-gray-500'>No pets found.</p>";
                    }
                    ?>
                </div>
            </div>
        </section>
    </main>
</body>

</html>