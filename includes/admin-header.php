<header>
    <section class="p-10 w-full flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-shield text-2xl"></i>
                <h1 class="font-bold text-2xl">Admin Dashboard</h1>
            </div>
            <h2 class="text-lg font-semibold">Manage clinic operations and client accounts</h2>
        </div>
        <div>
            <a href="../admin/admin-settings.php"
                class="text-gray text-xs font-semibold border border-green-400 px-4 py-2 rounded-lg hover:bg-green-300 <?= basename($_SERVER['PHP_SELF']) == 'admin-settings.php' ? 'settings-active' : '' ?>"><i
                    class="fa-solid fa-cog mr-2"></i>Settings</a>
            <a class="text-gray text-xs font-semibold border border-green-400 px-4 py-2 rounded-lg hover:bg-green-300"
                href="../logout.php"><i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Sign Out</a>
        </div>
    </section>

    <div class="px-10 w-full flex items-center justify-between">
        <nav class="w-full">
            <ul class="flex space-x-4 text-gray-700 text-xs font-semibold">
                <a href="../admin/admin-dashboard.php"
                    class="flex-1 text-center shadow-md font-semibold py-2 rounded-full border duration-200 hover:border-green-500 <?= basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php' ? 'active' : 'bg-white' ?>">
                    <li>Overview</li>
                </a>
                <a href="../admin/client-management.php"
                    class="flex-1 text-center shadow-md font-semibold py-2 rounded-full border duration-200 hover:border-green-500 <?= basename($_SERVER['PHP_SELF']) == 'client-management.php' ? 'active' : 'bg-white' ?>">
                    <li>Client Management</li>
                </a>
                <a href="../admin/pet-management.php"
                    class="flex-1 text-center shadow-md font-semibold py-2 rounded-full border duration-200 hover:border-green-500 <?= basename($_SERVER['PHP_SELF']) == 'pet-management.php' ? 'active' : 'bg-white' ?>">
                    <li>Patients</li>
                </a>
                <a href="../admin/medical-records.php"
                    class="flex-1 text-center shadow-md font-semibold py-2 rounded-full border duration-200 hover:border-green-500 <?= basename($_SERVER['PHP_SELF']) == 'medical-records.php' ? 'active' : 'bg-white' ?>">
                    <li>Medical Records</li>
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

    .settings-active {
        background-color: #7ceca5ff;
        /* Tailwind green-500 */
        color: black;
    }
</style>