<header>
    <section class="p-10 w-full flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <?php if ($client): ?>
                <h1 class="font-semibold text-2xl">Welcome, <?= htmlspecialchars($client['name']); ?>!</h1>
                <?php endif; ?>
            </div>
            <h2 class="text-lg font-normal">Your pet's health dashboard</h2>
        </div>
        <div>
            <a class="text-gray text-xs font-semibold border border-green-400 px-4 py-2 rounded-lg hover:bg-green-300"
                href="../logout.php"><i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Sign Out</a>
        </div>
    </section>
    <div class="px-10 w-full flex items-center justify-between">
        <nav class="w-full">
            <ul class="flex space-x-4 text-gray-700 text-xs font-semibold">
                <a class="flex-1 text-center shadow-md font-semibold py-2 bg-white rounded-full border hover:border-green-500 s"
                    href="../owner/owner-dashboard.php">
                    <li>Overview</li>
                </a>
                <a class="flex-1 text-center shadow-md font-semibold py-2 bg-white rounded-full border hover:border-green-500"
                    href="../owner/my-pets.php">
                    <li>My Pets
                    </li>
                </a>
                <a class="flex-1 text-center shadow-md font-semibold py-2 bg-white rounded-full border hover:border-green-500"
                    href="../owner/medical-records.php">
                    <li>Medical Records</li>
                </a>
            </ul>
        </nav>
    </div>
</header>