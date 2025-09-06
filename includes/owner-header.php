<header class="max-w-[1400px] mx-auto">
    <section
        class="p-4 md:p-10 w-full flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0">
        <div class="w-full md:w-auto">
            <div class="flex items-center space-x-2">
                <?php if ($client): ?>
                    <h1 class="font-bold text-xl md:text-2xl">Welcome, <?= htmlspecialchars($client['name']); ?>!</h1>
                <?php endif; ?>
            </div>
            <h2 class="text-base md:text-lg font-semibold text-gray-600">Your pet's health dashboard</h2>
        </div>
        <div class="w-full md:w-auto flex justify-end">
            <a class="text-gray-700 text-xs font-semibold border border-green-400 px-3 py-2 md:px-4 rounded-lg hover:bg-green-300 transition-colors"
                href="../logout.php">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>
                <span class="hidden sm:inline">Sign Out</span>
                <span class="sm:hidden">Logout</span>
            </a>
        </div>
    </section>

    <div class="px-4 md:px-10 w-full flex items-center justify-between">
        <nav class="w-full">
            <ul
                class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 text-gray-700 text-xs font-semibold">
                <a href="../owner/owner-dashboard.php"
                    class="flex-1 text-center shadow-md font-semibold py-3 sm:py-2 rounded-full border duration-200 hover:border-green-500 <?= basename($_SERVER['PHP_SELF']) == 'owner-dashboard.php' ? 'active' : 'bg-white' ?>">
                    <li class="flex items-center justify-center sm:block">
                        <i class="fas fa-chart-line mr-2 sm:hidden"></i>
                        Overview
                    </li>
                </a>
                <a href="../owner/my-pets.php"
                    class="flex-1 text-center shadow-md font-semibold py-3 sm:py-2 rounded-full border duration-200 hover:border-green-500 <?= basename($_SERVER['PHP_SELF']) == 'my-pets.php' ? 'active' : 'bg-white' ?>">
                    <li class="flex items-center justify-center sm:block">
                        <i class="fas fa-paw mr-2 sm:hidden"></i>
                        My Pets
                    </li>
                </a>
                <a href="../owner/medical-records.php"
                    class="flex-1 text-center shadow-md font-semibold py-3 sm:py-2 rounded-full border duration-200 hover:border-green-500 <?= basename($_SERVER['PHP_SELF']) == 'medical-records.php' ? 'active' : 'bg-white' ?>">
                    <li class="flex items-center justify-center sm:block">
                        <i class="fas fa-file-medical mr-2 sm:hidden"></i>
                        Medical Records
                    </li>
                </a>
            </ul>
        </nav>
    </div>
</header>

<style>
    .active {
        background-color: #22c55e;
        /* Tailwind green-500 */
        color: white;
        border-color: #22c55e;
    }

    /* Mobile specific styles */
    @media (max-width: 640px) {
        .active {
            background-color: #22c55e;
            border-color: #22c55e;
        }

        nav ul {
            gap: 0.5rem;
        }

        nav a {
            min-height: 48px;
            /* Better touch targets on mobile */
        }
    }
</style>