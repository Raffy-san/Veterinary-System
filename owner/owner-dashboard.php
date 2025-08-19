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
    <title>Owner Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-green-100 w-full">
    <?php
    include_once '../includes/owner-header.php';
    ?>
    <main>
        <section class="p-10 flex flex-col md:flex-row gap-8 w-full">
            <div class="bg-white rounded-lg shadow-md p-8 flex-1">
                <div class="w-full flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Total Pets</h3>
                    <i class="fa-solid fa-paw"></i>
                </div>
                <h4 class="font-semibold"><?= $petCount['total']; ?></h4>
                <h4 class="text-gray-500">Registered Pets</h4>
            </div>
            <div class="bg-white rounded-lg shadow-md p-8 flex-1">
                <div class="w-full flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Medical Records</h3>
                    <i class="fa-solid fa-file"></i>
                </div>
                <h4 class="font-semibold">3</h4>
                <h4 class="text-gray-500">Total Records</h4>
            </div>
        </section>
        <section class="px-10 flex flex-col md:flex-row gap-8 w-full mb-6">
            <div class="bg-white rounded-lg shadow-md p-8 flex-1">
                <div class="mb-4">
                    <h3 class="font-semibold">Your Contact information</h3>
                    <h4 class="text-gray-500">Information we have on the file</h4>
                </div>
                <div>
                    <p><i
                            class="fa-solid fa-envelope text-green-500 mr-2 mb-2"></i><?= htmlspecialchars($client['email']); ?>
                    </p>
                    <p><i
                            class="fa-solid fa-phone text-green-500 mr-2 mb-2"></i><?= htmlspecialchars($client['phone']); ?>
                    </p>
                    <p><i class="fa-solid fa-map-marker-alt text-green-500 mr-2 mb-2"></i>
                        <?= htmlspecialchars($client['address']); ?></p>
                </div>
            </div>
        </section>
    </main>
</body>

</html>