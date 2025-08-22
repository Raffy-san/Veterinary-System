<?php
include_once '../config/config.php';
require_once '../functions/session.php';
require_once '../helpers/fetch.php';
SessionManager::requireLogin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/green-paw.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Patients</title>
</head>

<body class="bg-green-100 w-full h-screen overflow-y-auto">
    <?php include_once '../includes/admin-header.php'; ?>

    <main class="p-10">
        <section class="p-10 bg-white rounded-lg shadow-md">
            <!-- Header & Filter -->
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-lg">Pet Management</h3>
                    <h4 class="text-gray-600">Manage Pet information</h4>
                </div>
                <div class="relative inline-block mt-4">
                    <select id="speciesFilter"
                        class="appearance-none w-32 px-4 py-2 pr-8 rounded-lg text-xs font-semibold text-gray-700
               bg-gradient-to-r from-green-100 to-green-200 border border-green-500 
               hover:from-green-200 hover:to-green-300 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
                        <option value="">Show All</option>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Bird">Bird</option>
                        <option value="Rabbit">Rabbit</option>
                        <option value="Other">Other</option>
                    </select>
                    <!-- Dropdown Icon -->
                    <span class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                        <i class="fa-solid fa-chevron-down text-green-600"></i>
                    </span>
                </div>

            </div>

            <!-- Search -->
            <div class="mb-4">
                <form>
                    <i class="fa-solid fa-search text-sm"></i>
                    <input type="search" id="search" placeholder="Search Pets..."
                        class="bg-gray-100 rounded px-3 py-2 mb-4 text-sm w-64">
                </form>
            </div>

            <!-- Table -->
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-sm text-left">
                        <th class="font-semibold">Name</th>
                        <th class="font-semibold">Species/Breed</th>
                        <th class="font-semibold">Age</th>
                        <th class="font-semibold">Gender</th>
                        <th class="font-semibold">Owner</th>
                        <th class="font-semibold">Contact</th>
                        <th class="font-semibold text-center">Status</th>
                        <th class="text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody id="petsBody">
                    <?php
                    $pets = fetchAllData(
                        $pdo,
                        "SELECT p.id, p.name AS pet_name, p.species, p.breed, p.age, p.gender,
                                o.name AS owner_name, o.phone AS owner_phone, o.email AS owner_email
                         FROM pets p
                         JOIN owners o ON p.owner_id = o.id"
                    );

                    foreach ($pets as $row) {
                        $speciesLower = strtolower(trim($row['species']));
                        echo "<tr class='border-b hover:bg-green-50 text-sm' data-species='{$speciesLower}'>";
                        echo "<td class='py-2'>{$row['pet_name']}</td>";
                        echo "<td class='py-2 flex flex-col'>
                                <span>{$row['species']}</span>
                                <span class='text-gray-500'>{$row['breed']}</span>
                              </td>";
                        echo "<td class='py-2'>{$row['age']} <span>Years</span></td>";
                        echo "<td class='py-2'>{$row['gender']}</td>";
                        echo "<td class='py-2'>{$row['owner_name']}</td>";
                        echo "<td class='flex flex-col py-2'>
                                <span><i class='fa-solid fa-envelope text-green-600'></i> {$row['owner_email']}</span>
                                <span class='text-gray-500 text-xs'><i class='fa-solid fa-phone'></i> {$row['owner_phone']}</span>
                              </td>";
                        echo "<td class='text-center py-2'>
                                <span class='bg-green-500 text-white py-1 px-2 rounded text-xs font-semibold'>Active</span>
                              </td>";
                        echo "<td class='text-right py-2'>
                                <button class='fa-solid fa-eye text-gray-700 mr-2 bg-green-100 p-1.5 rounded border border-green-200 hover:bg-green-300'></button>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                    <tr id="noResults" class="hidden">
                        <td colspan="8" class="text-center py-4 text-gray-500">No results found</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>

    <!-- JS -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.getElementById('search');
            const filterSelect = document.getElementById('speciesFilter');
            const rows = document.querySelectorAll("#petsBody tr");

            function applyFilters() {
                const searchTerm = searchInput.value.toLowerCase();
                const filterValue = filterSelect.value.toLowerCase();

                rows.forEach(row => {
                    const species = row.getAttribute('data-species') || '';
                    const name = row.cells[0].textContent.toLowerCase();
                    const speciesBreed = row.cells[1].textContent.toLowerCase();
                    const owner = row.cells[4].textContent.toLowerCase();

                    const matchesSearch = name.includes(searchTerm) || speciesBreed.includes(searchTerm) || owner.includes(searchTerm);
                    const matchesFilter = !filterValue || species === filterValue;

                    row.style.display = (matchesSearch && matchesFilter) ? "" : "none";
                });
            }

            searchInput.addEventListener('input', applyFilters);
            filterSelect.addEventListener('change', applyFilters);
        });
    </script>
</body>

</html>