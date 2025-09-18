<header class="max-w-[1400px] mx-auto">
    <section class="p-4 md:p-10 w-full flex flex-row items-center justify-between space-y-0">
        <div class="flex-1">
            <div class="flex items-center space-x-2">
                <?php if ($client): ?>
                    <h1 class="font-bold text-base sm:text-lg md:text-2xl">
                        Welcome, <?= htmlspecialchars($client['name']); ?>!
                    </h1>
                <?php endif; ?>
            </div>
            <h2 class="text-xs sm:text-sm md:text-lg font-semibold text-gray-600">
                Your pet's health dashboard
            </h2>
        </div>
        <div class="flex-shrink-0">
            <a class="text-gray-700 text-xs sm:text-sm font-semibold border border-green-400 px-3 py-2 md:px-4 rounded-lg hover:bg-green-300 transition-colors"
                href="../logout.php">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>
                <span class="hidden sm:inline">Sign Out</span>
                <span class="sm:hidden">Logout</span>
            </a>
        </div>
    </section>

    <!-- Navigation -->
    <div class="px-4 md:px-10 w-full">
        <nav class="w-full">
            <ul
                class="flex flex-row justify-between sm:justify-start gap-1 sm:gap-4 text-gray-700 text-xs sm:text-sm font-semibold">
                <li class="flex-1 text-center">
                    <a href="../owner/owner-dashboard.php"
                        class="flex items-center justify-center gap-1 sm:gap-2 shadow-sm py-4 sm:py-2 px-2 sm:px-4 rounded-full border border-gray-300 duration-200 hover:border-green-500 min-h-[44px] text-xs sm:text-sm <?= basename($_SERVER['PHP_SELF']) == 'owner-dashboard.php' ? 'bg-green-500 text-white border-green-500' : 'bg-white' ?>">
                        <!-- HIDE on mobile -->
                        <i class="fas fa-chart-line hidden sm:inline-block"></i>
                        <span>Overview</span>
                    </a>
                </li>

                <li class="flex-1 text-center">
                    <a href="../owner/my-pets.php"
                        class="flex items-center justify-center gap-1 sm:gap-2 shadow-sm py-4 sm:py-2 px-2 sm:px-4 rounded-full border border-gray-300 duration-200 hover:border-green-500 min-h-[44px] text-xs sm:text-sm <?= basename($_SERVER['PHP_SELF']) == 'my-pets.php' ? 'bg-green-500 text-white border-green-500' : 'bg-white' ?>">
                        <!-- SHOW on mobile, hide on sm+ -->
                        <i class="fas fa-paw inline-block sm:hidden"></i>
                        <span>My Pets</span>
                    </a>
                </li>

                <li class="flex-1 text-center">
                    <a href="../owner/medical-records.php"
                        class="flex items-center justify-center gap-1 sm:gap-2 shadow-sm py-4 sm:py-2 px-2 sm:px-4 rounded-full border border-gray-300 duration-200 hover:border-green-500 min-h-[44px] text-xs sm:text-sm <?= basename($_SERVER['PHP_SELF']) == 'medical-records.php' ? 'bg-green-500 text-white border-green-500' : 'bg-white' ?>">
                        <!-- HIDE on mobile -->
                        <i class="fas fa-file-medical hidden sm:inline-block"></i>
                        <span>Medical Records</span>
                    </a>
                </li>
            </ul>

        </nav>
    </div>

</header>

<style>
    .active {
        background-color: #22c55e;
        color: white;
        border-color: #22c55e;
    }

    @media (max-width: 640px) {
        nav a {
            min-height: 44px;
            /* Bigger touch targets */
            font-size: 0.75rem;
            /* smaller text for mobile */
        }
    }
</style>