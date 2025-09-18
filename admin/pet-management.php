<?php
include_once '../config/config.php';
require_once '../functions/session.php';
require_once '../helpers/fetch.php';
SessionManager::requireLogin();
SessionManager::requireRole('admin');

$admin = SessionManager::getUser($pdo);

if (!$admin) {
    SessionManager::logout('../login.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/green-paw.png">
    <link rel="stylesheet" href="../assets/css/global.css">
    <script src="../assets/js/script.js"></script>
    <link rel="stylesheet" href="../assets/css/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Patients Management</title>
</head>

<body class="bg-green-100 w-full min-h-screen overflow-y-auto">
    <?php include_once '../includes/admin-header.php'; ?>

    <main class="p-10 max-w-[1400px] mx-auto">
        <section class="p-10 bg-white rounded-lg shadow-md">
            <!-- Header & Filter -->
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-lg">Pet Management</h3>
                    <h4 class="text-gray-600">Manage Pet information</h4>
                </div>
                <div class="flex justify-between items-center space-x-4">
                    <h3 class="relative inline-block mt-4 font-semibold">Filter By Species:</h3>
                    <div class="relative inline-block mt-4">
                        <select id="speciesFilter"
                            class="appearance-none cursor-pointer w-32 px-4 py-2 pr-8 rounded-lg text-xs font-semibold text-gray-700
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
            </div>

            <!-- Search -->
            <div class="flex justify-between items-center">
                <div class="mb-4">
                    <form>
                        <i class="fa-solid fa-search text-sm"></i>
                        <input type="search" id="search" placeholder="Search Pets..."
                            class="bg-gray-100 rounded px-3 py-2 mb-4 text-sm w-64">
                    </form>
                </div>
                <div class="mb-4">
                    <h3 class="font-semibold"></h3>
                </div>
            </div>

            <!-- Table -->
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-sm text-left border-b border-gray-300">
                        <th class="font-semibold py-2">Name</th>
                        <th class="font-semibold py-2">Species/Breed</th>
                        <th class="font-semibold py-2">Age</th>
                        <th class="font-semibold py-2">Gender</th>
                        <th class="font-semibold py-2">Owner</th>
                        <th class="font-semibold py-2">Contact</th>
                        <th class="text-right font-semibold py-2">Actions</th>
                    </tr>
                </thead>
                <tbody id="petsBody">
                    <?php
                    $pets = fetchAllData(
                        $pdo,
                        "SELECT p.id AS pet_id, p.name AS pet_name, p.species, p.breed, p.age, p.gender, p.color, p.weight, p.notes,
                                o.name AS owner_name, o.phone AS owner_phone, o.email AS owner_email
                         FROM pets p
                         JOIN owners o ON p.owner_id = o.id"
                    );

                    foreach ($pets as $row) {
                        $speciesLower = strtolower(trim($row['species']));
                        echo "<tr class='border-b border-gray-300 hover:bg-green-50 text-sm' data-species='{$speciesLower}'>";
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
                        echo "<td class='text-right py-2'>
                                <button 
                                    data-modal='viewModal'
                                    data-id='" . $row['pet_id'] . "'
                                   data-name='" . htmlspecialchars($row['pet_name'] ?? '') . "'
                                    data-species='" . htmlspecialchars($row['species'] ?? '') . "'
                                    data-breed='" . htmlspecialchars($row['breed'] ?? '') . "'
                                    data-age='" . htmlspecialchars($row['age'] ?? '') . "'
                                    data-gender='" . htmlspecialchars($row['gender'] ?? '') . "'
                                    data-color='" . htmlspecialchars($row['color'] ?? '') . "'
                                    data-weight='" . htmlspecialchars($row['weight'] ?? '') . "'
                                    data-notes='" . htmlspecialchars($row['notes'] ?? '') . "'
                                    data-owner='" . htmlspecialchars($row['owner_name'] ?? '') . "'
                                    data-email='" . htmlspecialchars($row['owner_email'] ?? '') . "'
                                    data-phone='" . htmlspecialchars($row['owner_phone'] ?? '') . "'
                                    class='open-modal fa-solid fa-eye cursor-pointer text-gray-700 mr-2 bg-green-100 p-1.5 border rounded border-green-200 hover:bg-green-300'>
                                </button>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                    <tr id="noResults" class="hidden">
                        <td colspan="8" class="text-center py-4 text-gray-500">No results found</td>
                    </tr>
                </tbody>
            </table>
            <div id="pagination" class="flex justify-center space-x-2 mt-4"></div>
        </section>
        <div id="viewModal" class="modal hidden fixed inset-0 items-center justify-center"
            style="background-color: rgba(0,0,0,0.4);">
            <div class="custom-scrollbar bg-green-100 rounded-lg p-4 max-h-[60vh] max-w-[450px] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-m">Pet Details - <span id="petName"></span></h3>
                        <h4 class="text-gray-500 text-sm">Complete pet information</h4>
                    </div>
                    <button class="close text-xl cursor-pointer" aria-label="Close">&times;</button>
                </div>
                <div class="flex flex-row justify-between space-x-2">
                    <div id="petDetails" class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4 w-full">
                        <!-- row details will be populated here -->
                    </div>
                    <div id="ownerDetails" class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4 w-full">
                        <!-- Additional details will be populated here -->
                    </div>
                </div>
                <div id="notesDetails" class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4 w-full"
                    style="word-break: break-all; white-space: pre-wrap; overflow-wrap: anywhere;">
                    <!-- Additional details will be populated here -->
                </div>
            </div>
        </div>
    </main>

    <!-- JS -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const speciesCountDiv = document.querySelector('.flex.justify-between.items-center .mb-4 h3.font-semibold');
            const petDetails = document.getElementById("petDetails");
            const ownerDetails = document.getElementById("ownerDetails");
            const notesDetails = document.getElementById("notesDetails");
            const petNameSpan = document.getElementById("petName");
            const searchInput = document.getElementById('search');
            const filterSelect = document.getElementById('speciesFilter');
            const rows = document.querySelectorAll("#petsBody tr");
            const tableBody = document.getElementById("petsBody");
            const row = tableBody.querySelectorAll("tr");
            const pagination = document.getElementById("pagination");
            const rowsPerPage = 6;
            let currentPage = 1;
            const totalPages = Math.ceil(rows.length / rowsPerPage);

            function applyFilters() {
                const searchTerm = searchInput.value.toLowerCase();
                const filterValue = filterSelect.value.toLowerCase();
                let visibleCount = 0;

                rows.forEach(row => {
                    if (!row.cells.length || row.id === "noResults") return;

                    const species = row.getAttribute('data-species') || '';
                    const name = row.cells[0].textContent.toLowerCase();
                    const speciesBreed = row.cells[1].textContent.toLowerCase();
                    const owner = row.cells[4].textContent.toLowerCase();

                    const matchesSearch = name.includes(searchTerm) || speciesBreed.includes(searchTerm) || owner.includes(searchTerm);
                    const matchesFilter = !filterValue || species === filterValue;

                    const shouldShow = matchesSearch && matchesFilter;
                    row.style.display = shouldShow ? "" : "none";

                    if (shouldShow) {
                        visibleCount++; // just count all visible pets
                    }
                });

                document.getElementById('noResults').classList.toggle('hidden', visibleCount > 0);

                // Update species count display to total visible pets
                speciesCountDiv.textContent = `Total Pets: ${visibleCount}`;
            }

            searchInput.addEventListener('input', applyFilters);
            filterSelect.addEventListener('change', applyFilters);

            const addField = (container, label, value) => {
                const p = document.createElement("p");
                p.innerHTML = `<span class="font-semibold">${label}</span> ${value || "N/A"}`;
                container.appendChild(p);
            };

            document.querySelectorAll(".open-modal").forEach(btn => {
                btn.addEventListener("click", () => {
                    const modal = document.getElementById(btn.dataset.modal);
                    modal.classList.remove("hidden"); // ✅ Show modal
                    modal.classList.add("flex");
                    updateBodyScroll();

                    petDetails.innerHTML = "<h3 class='font-semibold mb-4'>Pet information</h3>";
                    ownerDetails.innerHTML = "<h4 class='font-semibold mb-4'>Owner information</h4>";
                    notesDetails.innerHTML = "<h4 class='font-semibold mb-4'>Notes</h4>";
                    petNameSpan.textContent = btn.dataset.name;

                    addField(petDetails, "Pet Name:", btn.dataset.name);
                    addField(petDetails, "Species:", btn.dataset.species);
                    addField(petDetails, "Breed:", btn.dataset.breed);
                    addField(petDetails, "Age:", btn.dataset.age || "None");
                    addField(petDetails, "Color:", btn.dataset.color || "None");
                    addField(petDetails, "Weight:", btn.dataset.weight || "None");

                    addField(ownerDetails, "Name:", btn.dataset.owner);
                    addField(ownerDetails, "Email:", btn.dataset.email);
                    addField(ownerDetails, "Phone:", btn.dataset.phone);

                    addField(notesDetails, "", btn.dataset.notes || "None");
                });
            });

            // Close modal
            document.querySelectorAll(".modal .close").forEach(btn => {
                btn.addEventListener("click", () => {
                    btn.closest(".modal").classList.add("hidden");
                    updateBodyScroll();
                });
            });

            // Close modal by clicking outside
            document.querySelectorAll(".modal").forEach(modal => {
                modal.addEventListener("click", e => {
                    if (e.target === modal) {
                        modal.classList.add("hidden");
                        updateBodyScroll();
                    }
                });
            });
            // Pagination
            const showPage = page => {
                currentPage = page;
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                row.forEach((row, i) => row.style.display = (i >= start && i < end) ? "" : "none");
                renderPagination();
            };

            const renderPagination = () => {
                pagination.innerHTML = "";
                if (currentPage > 1) {
                    const prev = document.createElement("button");
                    prev.className = "bg-blue-400 text-xs text-white py-1 px-2 rounded-lg";
                    prev.textContent = "Prev";
                    prev.onclick = () => showPage(currentPage - 1);
                    pagination.appendChild(prev);
                }
                for (let i = 1; i <= totalPages; i++) {
                    const btn = document.createElement("button");
                    btn.className = `text-xs py-1 px-2 rounded-lg ${i === currentPage ? "bg-green-500 text-white" : "bg-gray-200"}`;
                    btn.textContent = i;
                    btn.onclick = () => showPage(i);
                    pagination.appendChild(btn);
                }
                if (currentPage < totalPages) {
                    const next = document.createElement("button");
                    next.className = "bg-blue-400 text-xs text-white py-1 px-2 rounded-lg";
                    next.textContent = "Next";
                    next.onclick = () => showPage(currentPage + 1);
                    pagination.appendChild(next);
                }
            };

            showPage(1);

            applyFilters();
        });

    </script>
</body>

</html>