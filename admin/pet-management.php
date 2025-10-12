<?php
include_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/session.php';
require_once __DIR__ . '/../helpers/fetch.php';
SessionManager::requireLogin();
SessionManager::requireRole('admin');

$admin = SessionManager::getUser($pdo);

if (!$admin) {
    SessionManager::logout('../login.php');
}

if (empty($_SESSION['csrf_token'])) {
    $csrf_token = SessionManager::regenerateCsrfToken();
} else {
    $csrf_token = $_SESSION['csrf_token'];
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
                        <th class="font-semibold py-2">Status</th>
                        <th class="text-right font-semibold py-2">Actions</th>
                    </tr>
                </thead>
                <tbody id="petsBody">
                    <?php
                    $pets = fetchAllData(
                        $pdo,
                        "SELECT p.id AS pet_id, p.name AS pet_name, p.species, p.breed, p.age, p.age_unit, p.gender, p.color, p.weight, p.weight_unit, p.birth_date , p.notes, p.status, dr.id AS death_record_id, dr.cause_of_death, dr.date_of_death, dr.time_of_death, dr.recorded_by, dr.location_of_death, dr.remarks, c.certificate_number, c.certificate_date, c.certificate_issued, c.issued_by,
                                o.name AS owner_name, o.phone AS owner_phone, o.email AS owner_email
                         FROM pets p
                         LEFT JOIN owners o ON p.owner_id = o.id
                         LEFT JOIN death_records dr ON p.id = dr.pet_id
                         LEFT JOIN certificates c ON p.id = c.pet_id AND c.certificate_type = 'death_certificate'"
                    );

                    $status = [
                        'Alive' => ['bg' => 'bg-green-500', 'color' => 'text-white'],
                        'Dead' => ['bg' => 'bg-red-500', 'color' => 'text-white']
                    ];

                    foreach ($pets as $row) {
                        $speciesLower = strtolower(trim($row['species']));
                        $Type = $row['status'];
                        $typeinfo = $status[$Type];
                        echo "<tr class='border-b border-gray-300 hover:bg-green-50 text-sm' data-species='{$speciesLower}'>";
                        echo "<td class='py-2'>{$row['pet_name']}</td>";
                        echo "<td class='py-2 flex flex-col'>
                                <span>{$row['species']}</span>
                                <span class='text-gray-500'>{$row['breed']}</span>
                              </td>";
                        echo "<td class='py-2'>{$row['age']} <span>{$row['age_unit']}</span> old</td>";
                        echo "<td class='py-2'>{$row['gender']}</td>";
                        echo "<td class='py-2'>{$row['owner_name']}</td>";
                        echo "<td class='flex flex-col py-2'>
                                <span><i class='fa-solid fa-envelope text-green-600'></i> {$row['owner_email']}</span>
                                <span class='text-gray-500 text-xs'><i class='fa-solid fa-phone'></i> {$row['owner_phone']}</span>
                              </td>";
                        echo '<td class="py-2"><span class="' . $typeinfo['bg'] . ' ' . $typeinfo['color'] . ' text-xs font-semibold px-2.5 py-0.5 rounded">' . $row['status'] . '</td>';
                        echo "<td class='text-right py-2'>
                                <button 
                                    data-modal='viewModal'
                                    data-id='" . $row['pet_id'] . "'
                                    data-name='" . htmlspecialchars($row['pet_name'] ?? '') . "'
                                    data-species='" . htmlspecialchars($row['species'] ?? '') . "'
                                    data-breed='" . htmlspecialchars($row['breed'] ?? '') . "'
                                    data-age='" . htmlspecialchars($row['age'] ?? '') . "'
                                    data-ageunit='" . htmlspecialchars($row['age_unit'] ?? '') . "'
                                    data-gender='" . htmlspecialchars($row['gender'] ?? '') . "'
                                    data-color='" . htmlspecialchars($row['color'] ?? '') . "'
                                    data-weight='" . htmlspecialchars($row['weight'] ?? '') . "'
                                    data-weightunit='" . htmlspecialchars($row['weight_unit'] ?? '') . "'
                                    data-notes='" . htmlspecialchars($row['notes'] ?? '') . "'
                                    data-birthdate ='" . htmlspecialchars($row['birth_date']) . "'
                                    data-status='" . htmlspecialchars($row['status'] ?? '') . "'
                                    data-deathreason='" . htmlspecialchars($row['cause_of_death']) . "'
                                    data-deathdate='" . htmlspecialchars($row['date_of_death']) . "'
                                    data-deathtime='" . htmlspecialchars($row['time_of_death']) . "'
                                    data-deathlocation='" . htmlspecialchars($row['location_of_death']) . "'
                                    data-recordedby='" . htmlspecialchars($row['recorded_by']) . "'
                                    data-remarks='" . htmlspecialchars($row['remarks']) . "'
                                    data-owner='" . htmlspecialchars($row['owner_name'] ?? '') . "'
                                    data-email='" . htmlspecialchars($row['owner_email'] ?? '') . "'
                                    data-phone='" . htmlspecialchars($row['owner_phone'] ?? '') . "'
                                    class='open-modal fa-solid fa-eye cursor-pointer text-gray-700 mr-2 bg-green-100 p-1.5 border rounded border-green-200 hover:bg-green-300'>
                                </button>
                                
                                <button class='open-status-modal cursor-pointer font-semibold text-xs text-gray-700 mr-2 bg-green-100 p-1.5 border rounded border-green-200 hover:bg-green-300'  data-id='" . $row['pet_id'] . "'  data-name='" . htmlspecialchars($row['pet_name'] ?? '') . "'>Toggle Status</button>";
                        if (isset($row['status']) && strtolower($row['status']) === 'dead' && !empty($row['death_record_id'])) {
                            echo "
        <button 
            class='issue-certificate-modal cursor-pointer font-semibold text-xs text-gray-700 bg-red-100 p-1.5 border rounded border-red-200 hover:bg-red-300'
            data-death-record-id='" . htmlspecialchars($row['death_record_id']) . "'
            data-death-certificate-number='" . htmlspecialchars($row['certificate_number']) . "'
            data-pet-name='" . htmlspecialchars($row['pet_name']) . "'
        >
            Issue Death Certificate
        </button>";
                        }
                        echo "</td>";
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
                        <h3 class="font-semibold text-m">Pet Details - <span id="petName"></span>
                            <span id="statusBadge" class="text-xs font-semibold px-2 py-1 rounded"></span>
                        </h3>
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
                    style="word-break: break-word; white-space: pre-line;">
                    <!-- Additional details will be populated here -->
                </div>
                <div id="deathDetails" class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4 w-full hidden"
                    style="word-break: break-word; white-space: pre-line;">
                    <!-- Death details will be populated here -->
                </div>
            </div>
        </div>
        <!-- Toggle Pet Status Modal -->
        <div id="ToggleModal" class="modal hidden fixed inset-0 items-center justify-center"
            style="background-color: rgba(0,0,0,0.4);">
            <div
                class="custom-scrollbar bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative max-h-[80vh] overflow-y-auto">

                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-lg">Update Pet Status - <span id="togglePetName"
                                class="text-green-700"></span></h3>
                        <p class="text-gray-600 text-sm">Change status to <strong>Alive</strong> or
                            <strong>Dead</strong>
                        </p>
                    </div>
                    <button class="close text-2xl text-gray-700 hover:text-red-600 font-bold cursor-pointer"
                        aria-label="Close">&times;</button>
                </div>

                <!-- Form -->
                <form id="togglePetStatus" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="pet_id" id="togglePetId">

                    <!-- Status Selector -->
                    <div>
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Status</label>
                        <select id="statusSelect" name="status"
                            class="w-full border border-gray-300 bg-white px-2 py-1 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            required>
                            <option value="Alive">Alive</option>
                            <option value="Dead">Dead</option>
                        </select>
                    </div>

                    <!-- Death Details (Hidden by Default) -->
                    <div id="deathFields" class="space-y-4 hidden border-t border-gray-300 pt-4">
                        <h4 class="font-semibold text-gray-700 text-base">Death Details</h4>

                        <!-- Date and Time Row -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 mb-1 text-sm font-semibold">Date of Death</label>
                                <input type="date" name="death_date"
                                    class="w-full border border-gray-300 bg-white px-2 py-1 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-1 text-sm font-semibold">Time of Death</label>
                                <input type="time" name="death_time"
                                    class="w-full border border-gray-300 bg-white px-2 py-1 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Reason of Death</label>
                            <textarea name="death_reason" rows="2"
                                class="w-full border border-gray-300 bg-white px-2 py-1 rounded text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Recorded By</label>
                            <input type="text" name="recorded_by" placeholder="Veterinarian name"
                                class="w-full border border-gray-300 bg-white px-2 py-1 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Location of Death</label>
                            <input type="text" name="location_of_death" placeholder="Clinic, home, etc."
                                class="w-full border border-gray-300 bg-white px-2 py-1 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Remarks</label>
                            <textarea name="remarks" rows="2"
                                class="w-full border border-gray-300 bg-white px-2 py-1 rounded text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-500 "></textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="updateStatusButton"
                        class="w-full bg-green-500 text-white font-semibold px-4 py-2 rounded hover:bg-green-600 transition cursor-pointer">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>

        <div id="printModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center"
            style="background-color: rgba(0,0,0,0.4);">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <div class="flex flex-record items-center mb-4">
                    <i class="fa-solid fa-circle-question mr-2 text-green-600"></i>
                    <h3 class="font-semibold text-lg">Issue Death Certificate</h3>
                </div>
                <p id="printMessage" class="mb-4 text-sm text-gray-600">
                    <!-- print Message -->
                </p>
                <div class="flex justify-end space-x-2">
                    <button class="close px-3 py-2 bg-gray-300 rounded hover:bg-gray-400 text-xs">Cancel</button>
                    <a id="confirmPrintBtn" href="#"
                        class="issue-death-certificate px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-xs">
                        Issue
                    </a>
                </div>
            </div>
        </div>

        <?php include __DIR__ . '/../includes/message-modal.php' ?>
    </main>

    <!-- JS -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const speciesCountDiv = document.querySelector('.flex.justify-between.items-center .mb-4 h3.font-semibold');
            const petDetails = document.getElementById("petDetails");
            const ownerDetails = document.getElementById("ownerDetails");
            const notesDetails = document.getElementById("notesDetails");
            const deathDetails = document.getElementById("deathDetails");
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
            const statusSelect = document.getElementById("statusSelect");
            const deathFields = document.getElementById("deathFields");
            let csrfToken = "<?= $csrf_token ?>";
            const statusBadge = document.getElementById("statusBadge");

            const openModal = (modal) => {
                modal.classList.remove("hidden");
                modal.classList.add("flex");
                updateBodyScroll();
            };

            const closeModal = (modal) => {
                modal.classList.add("hidden");
                updateBodyScroll();
            };

            function closeAllModals() {
                document.querySelectorAll(".modal").forEach(modal => {
                    modal.classList.add("hidden");
                });
                updateBodyScroll();
            }

            // Find the toggle status modal and form
            const toggleModal = document.getElementById("ToggleModal");
            const toggleForm = document.getElementById("togglePetStatus");

            // Helper to reset the toggle modal form and hide death fields
            function resetToggleModal() {
                toggleForm.reset();
                document.getElementById("deathFields").style.display = "none";
            }

            // Attach close button functionality
            document.querySelectorAll(".modal .close").forEach(btn => {
                btn.addEventListener("click", () => {
                    const modal = btn.closest(".modal");
                    closeModal(modal);
                    if (modal === toggleModal) resetToggleModal();
                });
            });

            // Optional: close when clicking outside modal content
            document.querySelectorAll(".modal").forEach(modal => {
                modal.addEventListener("click", (e) => {
                    if (e.target === modal) {
                        closeModal(modal);
                        if (modal === toggleModal) resetToggleModal();
                    }
                });
            });

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
                    const status = btn.dataset.status ? btn.dataset.status.toLowerCase() : "";
                    modal.classList.remove("hidden"); // ✅ Show modal
                    modal.classList.add("flex");
                    updateBodyScroll();

                    deathDetails.classList.add("hidden");

                    petDetails.innerHTML = "<h3 class='font-semibold mb-4'>Pet information</h3>";
                    ownerDetails.innerHTML = "<h4 class='font-semibold mb-4'>Owner information</h4>";
                    notesDetails.innerHTML = "<h4 class='font-semibold mb-4'>Notes</h4>";

                    if (status === "dead") {
                        deathDetails.classList.remove("hidden");
                        deathDetails.innerHTML = "<h4 class='font-semibold mb-4'>Death Details</h4>";
                    }
                    petNameSpan.textContent = btn.dataset.name;

                    statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);

                    if (status === "alive") {
                        statusBadge.className = "text-xs font-semibold px-2 py-1 rounded bg-green-500 text-white";
                    } else {
                        statusBadge.className = "text-xs font-semibold px-2 py-1 rounded bg-red-500 text-white";
                    }

                    let formattedTime = "";
                    if (btn.dataset.deathtime) {
                        const [hour, minute] = btn.dataset.deathtime.split(":");
                        const dateObj = new Date();
                        dateObj.setHours(hour, minute);
                        formattedTime = dateObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    }

                    addField(petDetails, "Pet Name:", btn.dataset.name);
                    addField(petDetails, "Species:", btn.dataset.species);
                    addField(petDetails, "Breed:", btn.dataset.breed);
                    addField(petDetails, "Age:", `${btn.dataset.age} ${btn.dataset.ageunit}` || "None");
                    addField(petDetails, "Color:", btn.dataset.color || "None");
                    addField(petDetails, "Weight:", `${btn.dataset.weight} ${btn.dataset.weightunit}` || "None");
                    addField(petDetails, "Birth Date:", btn.dataset.birthdate);

                    addField(ownerDetails, "Name:", btn.dataset.owner);
                    addField(ownerDetails, "Email:", btn.dataset.email);
                    addField(ownerDetails, "Phone:", btn.dataset.phone);

                    addField(notesDetails, "", btn.dataset.notes || "None");

                    if (status === "dead") {
                        addField(deathDetails, "Date of Death:", btn.dataset.deathdate);
                        addField(deathDetails, "Time of Death:", `${formattedTime}`);
                        addField(deathDetails, "Reason of Death:", btn.dataset.deathreason);
                        addField(deathDetails, "Location of Death:", btn.dataset.deathlocation);
                        addField(deathDetails, "Recorded By:", btn.dataset.recordedby);
                        addField(deathDetails, "Remarks:", btn.dataset.remarks);
                    }
                });
            });

            document.querySelectorAll(".open-status-modal").forEach(btn => {
                btn.addEventListener("click", () => {
                    openModal(document.getElementById("ToggleModal"));
                });
            });

            // ===================== GENERIC MODAL CLOSE =====================
            document.querySelectorAll(".modal .close").forEach(btn => {
                btn.addEventListener("click", () => closeModal(btn.closest(".modal")));
            });
            document.querySelectorAll(".modal").forEach(modal => {
                modal.addEventListener("click", e => { if (e.target === modal) closeModal(modal); });
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



            statusSelect.addEventListener("change", () => {
                deathFields.style.display = statusSelect.value === "Dead" ? "block" : "none";
            });

            // When clicking "Toggle Status" button, set pet_id and pet_name
            document.querySelectorAll(".open-status-modal").forEach(btn => {
                btn.addEventListener("click", () => {
                    const modal = document.getElementById("ToggleModal");
                    openModal(modal);

                    const petId = btn.dataset.id; // ✅ numeric ID
                    const petName = btn.dataset.name; // ✅ pet name

                    document.getElementById("togglePetId").value = petId;
                    document.getElementById("togglePetName").textContent = petName;
                });
            });

            document.getElementById("togglePetStatus").addEventListener("submit", async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append("csrf_token", "<?= $csrf_token ?>");

                const response = await fetch("../php/Toggle/toggle-pet.php", {
                    method: "POST",
                    body: formData
                });
                const data = await response.json();

                if (data.csrf_token) csrfToken = data.csrf_token;

                if (data.status === "success") {
                    showMessage("Success", "Pet status updated successfully!", () => location.reload());
                } else {
                    showMessage("Error", data.message);
                }
            });

            document.querySelectorAll(".issue-certificate-modal").forEach(btn => {
                btn.addEventListener("click", () => {
                    const recordId = btn.dataset.deathRecordId;
                    const modal = document.getElementById("printModal");
                    const petName = btn.dataset.petName;
                    modal.classList.remove("hidden");
                    modal.classList.add("flex");
                    updateBodyScroll();

                    document.getElementById("confirmPrintBtn").dataset.id = recordId;
                    document.getElementById("printMessage").textContent =
                        `Issue death certificate for ${petName}?`;
                });
            });

            document.getElementById("confirmPrintBtn").addEventListener("click", async () => {
                const deathRecordId = document.getElementById("confirmPrintBtn").dataset.id;
                const petName = document.getElementById("printMessage").textContent;

                try {
                    const response = await fetch("../php/Toggle/issue-certificate.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: new URLSearchParams({
                            csrf_token: csrfToken,
                            death_record_id: deathRecordId
                        })
                    });

                    const data = await response.json();

                    // Update CSRF token for next request
                    if (data.csrf_token) csrfToken = data.csrf_token;

                    if (data.status === "success") {
                        closeAllModals();
                        showMessage(
                            "Certificate Issued",
                            `✅ Certificate issued for ${petName}\nCertificate No: ${data.certificate_number}`,
                            () => {
                                // Open PDF in new tab
                                const win = window.open(`../print/death-certificate.php?id=${deathRecordId}`, "_blank");
                                if (!win) showMessage("Popup Blocked", "Please allow popups to view the certificate.");
                            }
                        );
                    } else {
                        showMessage("Error", "❌ " + data.message);
                    }

                } catch (error) {
                    console.error("Error issuing certificate:", error);
                    showMessage("Error", "An unexpected error occurred. Please try again.");
                }
            });

            // =================== Helper: Show message modal ===================
            function showMessage(title, text, callback) {
                const msgModal = document.getElementById("messageModal");
                const msgTitle = document.getElementById("messageTitle");
                const msgText = document.getElementById("messageText");
                const okBtn = document.getElementById("closeMessageBtn");

                msgTitle.textContent = title;
                msgText.textContent = text;
                msgModal.classList.remove("hidden");
                msgModal.classList.add("flex");
                updateBodyScroll();

                // Remove previous listeners
                okBtn.replaceWith(okBtn.cloneNode(true));
                const newOkBtn = document.getElementById("closeMessageBtn");

                newOkBtn.addEventListener("click", () => {
                    msgModal.classList.add("hidden");
                    updateBodyScroll();
                    if (typeof callback === "function") callback();
                });
            }
        });
    </script>
</body>

</html>