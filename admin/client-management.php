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
    <title>Client Management</title>
</head>

<body class="bg-green-100 w-full h-screen overflow-y-auto">
    <?php include_once '../includes/admin-header.php'; ?>

    <main class="p-10">
        <!-- Add Client Modal -->
        <div id="addClientModal"
            class="modal fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div class="bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-m font-semibold">Create Client Account</h3>
                        <h4 class="text-sm text-gray-600">Add new client to the system</h4>
                    </div>
                    <button class="close text-xl" aria-label="Close">&times;</button>
                </div>
                <form class="flex flex-wrap items-center justify-between" method="POST" action="add-client.php">
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Name</label>
                        <input type="text" name="name" required
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Client Name">
                    </div>
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Username</label>
                        <input type="text" name="username" required
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Client Username">
                    </div>
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Password</label>
                        <input type="password" name="password" required
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Client Password">
                    </div>
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Email</label>
                        <input type="email" name="email"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Client Email">
                    </div>
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Phone</label>
                        <input type="tel" name="phone" required pattern="^09\d{9}$"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="09XXXXXXXXX">
                    </div>
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Emergency Contact</label>
                        <input type="tel" name="emergency_contact" pattern="^09\d{9}$"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="09XXXXXXXXX">
                    </div>
                    <div class="mb-4 w-full">
                        <label class="block text-gray-700 mb-1 text-sm">Address</label>
                        <input type="text" name="address" required
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Enter Client Full Address">
                    </div>
                    <div class="flex justify-end w-full">
                        <button type="button"
                            class="close mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700 text-sm">Create
                            Account</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Client Table -->
        <section class="p-10 bg-white rounded-lg shadow-md">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-lg">Client Management</h3>
                    <h4 class="text-gray-600">Manage client information and accounts</h4>
                </div>
                <button data-modal="addClientModal"
                    class="open-modal mt-4 px-4 py-2 bg-green-700 text-white rounded-lg text-xs hover:bg-green-800">
                    <i class="fa-solid fa-plus mr-2"></i>Add Client
                </button>
            </div>

            <div class="mb-4">
                <form>
                    <i class="fa-solid fa-search text-sm"></i>
                    <input type="search" id="search" placeholder="Search Clients..."
                        class="bg-gray-100 rounded px-3 py-2 mb-4 text-sm w-64">
                </form>
            </div>

            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-sm text-left">
                        <th class="font-semibold">Name</th>
                        <th class="font-semibold">Contact</th>
                        <th class="font-semibold">Join Date</th>
                        <th class="font-semibold">Pets</th>
                        <th class="font-semibold text-center">Status</th>
                        <th class="font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="clientsBody">
                    <?php
                    $clients = fetchAllData($pdo, "SELECT 
                        o.id AS owner_id, 
                        o.name, 
                        o.email, 
                        o.phone, 
                        o.created_at, 
                        o.active,
                        GROUP_CONCAT(p.name SEPARATOR ', ') AS pets,
                        COUNT(p.id) AS pet_count
                    FROM owners o
                    LEFT JOIN pets p ON o.id = p.owner_id
                    GROUP BY o.id ORDER BY o.created_at DESC");

                    foreach ($clients as $client) {
                        $status = $client['active'] ? "Active" : "Inactive";
                        $statusClass = $client['active']
                            ? "bg-green-600 text-white"
                            : "bg-gray-400 text-white";

                        echo '<tr class="border-b hover:bg-green-50 text-sm text-left">';
                        echo '<td class="py-2">' . htmlspecialchars($client['name']) . '</td>';
                        echo '<td class="py-2 flex flex-col">' . '<span><i class="fa-solid fa-envelope text-green-600"></i>&nbsp;' . htmlspecialchars($client['email']) . '</span>' . '<span class="text-gray-500 text-xs"><i class="fa-solid fa-phone">&nbsp;</i>' . htmlspecialchars($client['phone']) . '</span></td>';
                        echo '<td class="py-2">' . htmlspecialchars($client['created_at']) . '</td>';
                        echo '<td class="py-2"><span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">' . $client['pet_count'] . '</span></td>';
                        echo '<td class="py-2 text-center"><span class="text-xs font-semibold px-2.5 py-0.5 rounded ' . $statusClass . '">' . $status . '</span></td>';
                        echo '<td class="py-2 text-right">
                                <button 
                                    data-modal="viewModal" 
                                    data-id="' . $client['owner_id'] . '"
                                    data-name="' . htmlspecialchars($client['name']) . '"
                                    data-email="' . htmlspecialchars($client['email']) . '"
                                    data-phone="' . htmlspecialchars($client['phone']) . '"
                                    data-created="' . htmlspecialchars($client['created_at']) . '"
                                    data-active="' . $status . '"
                                    class="open-modal fa-solid fa-eye text-gray-700 mr-2 bg-green-100 p-1.5 border rounded border-green-200 hover:bg-green-300">
                                </button>
                                <button class="fa-solid fa-pencil-alt text-gray-700 mr-2 bg-green-100 p-1.5 rounded border border-green-200 hover:bg-green-300"></button>
                                <button class="text-xs font-semibold mr-2 bg-green-100 p-1.5 border rounded border-green-200 hover:bg-green-300">Deactivate</button>
                                <button class="fa-solid fa-trash text-gray-700 mr-2 bg-green-100 p-1.5 border rounded border-green-200 hover:bg-green-300"></button>
                              </td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
            <div id="pagination" class="flex justify-center space-x-2 mt-4"></div>
        </section>

        <!-- View Modal -->
        <div id="viewModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-4">
                <h3 class="font-semibold mb-2">Client Details</h3>
                <div id="clientDetails"></div>
                <button class="close mt-4 bg-green-500 text-white py-2 px-4 rounded">Close</button>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const clientDetails = document.getElementById("clientDetails");
            const pagination = document.getElementById("pagination");
            const rowsPerPage = 6;
            const tableBody = document.getElementById("clientsBody");
            const rows = tableBody.querySelectorAll("tr");
            const totalPages = Math.ceil(rows.length / rowsPerPage);
            let currentPage = 1;

            // Open modal
            document.querySelectorAll(".open-modal").forEach(button => {
                button.addEventListener("click", () => {
                    const modal = document.getElementById(button.dataset.modal);
                    clientDetails.innerHTML = "";

                    // Build details safely
                    const fields = ["name", "email", "phone", "created", "active"];
                    fields.forEach(field => {
                        const p = document.createElement("p");
                        p.innerHTML = `<strong>${field.charAt(0).toUpperCase() + field.slice(1)}:</strong> ${button.dataset[field]}`;
                        clientDetails.appendChild(p);
                    });

                    modal.classList.remove("hidden");
                    document.body.style.overflow = "hidden";
                });
            });

            // Close modal
            document.querySelectorAll(".modal .close").forEach(closeBtn => {
                closeBtn.addEventListener("click", () => {
                    closeBtn.closest(".modal").classList.add("hidden");
                    document.body.style.overflow = "auto";
                });
            });

            // Close modal when clicking outside
            document.querySelectorAll(".modal").forEach(modal => {
                modal.addEventListener("click", e => {
                    if (e.target === modal) {
                        modal.classList.add("hidden");
                        document.body.style.overflow = "auto";
                    }
                });
            });

            // Search
            document.getElementById('search').addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                rows.forEach(row => {
                    const name = row.cells[0].textContent.toLowerCase();
                    const contact = row.cells[1].textContent.toLowerCase();
                    row.style.display = (name.includes(searchTerm) || contact.includes(searchTerm)) ? '' : 'none';
                });
            });

            // Pagination
            function showPage(page) {
                currentPage = page;
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                rows.forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? "" : "none";
                });
                renderPagination();
            }

            function renderPagination() {
                pagination.innerHTML = "";
                if (currentPage > 1) {
                    const prev = document.createElement("button");
                    prev.textContent = "Prev";
                    prev.className = "text-xs px-3 py-1 bg-gray-200 rounded";
                    prev.onclick = () => showPage(currentPage - 1);
                    pagination.appendChild(prev);
                }
                for (let i = 1; i <= totalPages; i++) {
                    const btn = document.createElement("button");
                    btn.textContent = i;
                    btn.className = "text-xs px-3 py-1 rounded " + (i === currentPage ? "bg-green-500 text-white" : "bg-gray-200");
                    btn.onclick = () => showPage(i);
                    pagination.appendChild(btn);
                }
                if (currentPage < totalPages) {
                    const next = document.createElement("button");
                    next.textContent = "Next";
                    next.className = "text-xs px-3 py-1 bg-gray-200 rounded";
                    next.onclick = () => showPage(currentPage + 1);
                    pagination.appendChild(next);
                }
            }
            showPage(1);
        });
    </script>
</body>

</html>