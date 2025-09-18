<?php
include_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/session.php';
require_once __DIR__ . '/../helpers/fetch.php';
require_once __DIR__ . '/../functions/crud.php';
SessionManager::requireLogin();
SessionManager::requireRole('admin');

$admin = SessionManager::getUser($pdo);

if (!$admin) {
    SessionManager::logout('../login.php');
}

$csrf_token = $_SESSION['csrf_token'] ?? SessionManager::regenerateCsrfToken();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/green-paw.png">
    <link rel="stylesheet" href="../assets/css/output.css">
    <script src="../assets/js/script.js"></script>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Client Management</title>
</head>

<body class="bg-green-100 w-full min-h-screen overflow-y-auto">
    <?php include_once '../includes/admin-header.php'; ?>

    <main class="p-10 max-w-[1400px] mx-auto">
        <!-- Main Client Table -->
        <section class="p-10 bg-white rounded-lg shadow-md">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-lg">Client Management</h3>
                    <h4 class="text-gray-600">Manage client information and accounts</h4>
                </div>
                <button data-modal="addClientModal"
                    class="open-modal mt-4 px-4 py-2 bg-green-600 text-white rounded-lg text-xs hover:bg-green-700 cursor-pointer">
                    <i class="fa-solid fa-plus mr-2"></i>Add Client
                </button>
            </div>

            <div class="mb-4">
                <form>
                    <i class="fa-solid fa-search text-sm"></i>
                    <input type="search" id="search" placeholder="Search Clients..."
                        class="bg-gray-100 rounded px-3 py-2 mb-4 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-green-500">
                </form>
            </div>

            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-sm text-left border-b border-gray-300">
                        <th class="font-semibold py-2">Name</th>
                        <th class="font-semibold py-2">Contact</th>
                        <th class="font-semibold py-2">Join Date</th>
                        <th class="font-semibold py-2">Pets</th>
                        <th class="font-semibold py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="clientsBody">
                    <?php
                    $clients = fetchAllData($pdo, "SELECT 
                                o.id AS owner_id, 
                                o.user_id,
                                o.name, 
                                o.email, 
                                o.emergency,
                                o.phone,
                                o.created_at,
                                o.address, 
                                GROUP_CONCAT(p.name SEPARATOR ', ') AS pets,
                                COUNT(p.id) AS pet_count 
                            FROM owners o
                            LEFT JOIN pets p ON o.id = p.owner_id
                            GROUP BY o.id ORDER BY o.created_at DESC
                            ");
                    foreach ($clients as $client) {

                        echo '<tr class="border-b border-gray-300 hover:bg-green-50 text-sm text-left">';
                        echo '<td class="py-2">' . htmlspecialchars($client['name']) . '</td>';
                        echo '<td class="py-2 flex flex-col">' . '<span><i class="fa-solid fa-envelope text-green-600"></i>&nbsp;' . htmlspecialchars($client['email']) . '</span>' . '<span class="text-gray-500 text-xs"><i class="fa-solid fa-phone">&nbsp;</i>' . htmlspecialchars($client['phone']) . '</span></td>';
                        echo '<td class="py-2">' . date('Y-m-d', strtotime($client['created_at'])) . '</td>';
                        echo '<td class="py-2"><span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">' . $client['pet_count'] . '</span></td>';
                        echo '<td class="py-2 text-right">
                                <button 
                                    data-modal="viewModal" 
                                    data-id="' . $client['owner_id'] . '"
                                    data-name="' . htmlspecialchars($client['name'] ?? '') . '"
                                    data-email="' . htmlspecialchars($client['email'] ?? '') . '"
                                    data-phone="' . htmlspecialchars($client['phone'] ?? '') . '"
                                    data-emergency="' . htmlspecialchars($client['emergency'] ?? '') . '"
                                    data-created="' . date('Y-m-d', strtotime($client['created_at'])) . '"
                                    data-address="' . htmlspecialchars($client['address'] ?? '') . '"
                                    data-petcount="' . $client['pet_count'] . '"
                                    class="open-modal fa-solid fa-eye cursor-pointer text-gray-700 mr-2 bg-green-100 p-1.5 border rounded border-green-200 hover:bg-green-300">
                                </button>
                                <button class="open-update-modal fa-solid fa-pencil-alt cursor-pointer text-gray-700 mr-2 bg-green-100 p-1.5 rounded border border-green-200 hover:bg-green-300" data-id="' . $client['owner_id'] . '"></button>
                                <button 
                                data-owner="' . $client['owner_id'] . '" 
                                class="open-pet-modal text-gray-700 cursor-pointer mr-2 bg-green-100 text-xs font-semibold p-1.5 rounded border border-green-200 hover:bg-green-300">
                                <i class="fa-solid fa-plus mr-1"></i>Add Pet
                                </button>
                                 <button
                                    data-modal="viewPetModal"
                                    data-owner="' . $client['owner_id'] . '"
                                 class="open-modal text-gray-700 cursor-pointer mr-2 bg-green-100 text-xs font-semibold p-1.5 rounded border border-green-200 hover:bg-green-300">View Pet</button>
                                <button 
                                    class="open-delete-modal fa-solid fa-trash cursor-pointer text-gray-700 mr-2 bg-green-100 p-1.5 border rounded border-green-200 hover:bg-red-400"
                                    data-id="' . $client['user_id'] . '" 
                                    data-name="' . htmlspecialchars($client['name']) . '">
                                </button>

                            </td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
            <div id="pagination" class="flex justify-center space-x-2 mt-4"></div>
        </section>

        <!-- Add Client Modal -->
        <div id="addClientModal"
            class="modal fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 hidden"
            style="background-color: rgba(0,0,0,0.4);">
            <div class="bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-m font-semibold">Create Client Account</h3>
                        <h4 class="text-sm text-gray-600">Add new client to the system</h4>
                    </div>
                    <button class="close text-xl cursor-pointer" aria-label="Close">&times;</button>
                </div>
                <form id="addClientForm" class="flex flex-wrap items-center justify-between" method="POST"
                    action="client-management.php">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Name</label>
                        <input type="text" name="name" required
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Client Name">
                    </div>
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Email</label>
                        <input type="email" name="email"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Client Email">
                    </div>
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Password</label>
                        <input type="password" name="password" required
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Client Password">
                    </div>
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Phone</label>
                        <input type="tel" name="phone" required pattern="^09\d{9}$"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="09XXXXXXXXX">
                    </div>
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Emergency Contact</label>
                        <input type="tel" name="emergency" pattern="^09\d{9}$"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="09XXXXXXXXX">
                    </div>
                    <div class="mb-4 w-full">
                        <label class="block text-gray-700 mb-1 text-sm">Address</label>
                        <input type="text" name="address" required
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Enter Client Full Address">
                    </div>
                    <div class="flex justify-end w-full">
                        <button type="button"
                            class="close cursor-pointer mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">Cancel</button>
                        <button type="submit" name="submit" id="addClient"
                            class="cursor-pointer px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700 text-sm">Create
                            Account</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Modal -->
        <div id="viewModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center"
            style="background-color: rgba(0,0,0,0.4);">
            <div class="custom-scrollbar bg-green-100 rounded-lg p-4 max-h-[70vh] max-w-[450px] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-m">Client Details - <span id="clientName"></span></h3>
                        <h4 class="text-gray-500 text-sm">Complete client information</h4>
                    </div>
                    <button class="close text-xl cursor-pointer" aria-label="Close">&times;</button>
                </div>
                <div class="flex flex-row justify-between space-x-2">
                    <div id="clientDetails" class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4 max-w-[220px]">
                        <!-- Client details will be populated here -->
                    </div>
                    <div id="additionalDetails" class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4">
                        <!-- Additional details will be populated here -->
                    </div>
                </div>
            </div>
        </div>

        <div id="updateClientModal"
            class="modal hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center"
            style="background-color: rgba(0,0,0,0.4);">
            <div class="bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-m">Update Client Information</h3>
                        <h4 class="text-gray-500 text-sm">Edit the details of the selected client</h4>
                    </div>
                    <button class="close text-xl cursor-pointer" aria-label="Close">&times;</button>
                </div>
                <form class="flex flex-wrap items-center justify-between" id="updateClientForm" method="POST"
                    action="client-management.php">
                    <!-- Owner ID -->
                    <input type="hidden" name="owner_id" id="updateClientId">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Name</label>
                        <input type="text" name="name" id="updateClientName" required
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Client Name">
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Email</label>
                        <input type="email" name="email" id="updateClientEmail"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Client Email">
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Password</label>
                        <input type="password" name="password" id="updateClientPassword"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Leave blank to keep current password">
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Phone</label>
                        <input type="tel" name="phone" id="updateClientPhone" required pattern="^09\d{9}$"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="09XXXXXXXXX">
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Emergency Contact</label>
                        <input type="tel" name="emergency" id="updateClientEmergency" pattern="^09\d{9}$"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="09XXXXXXXXX">
                    </div>

                    <div class="mb-4 w-full">
                        <label class="block text-gray-700 mb-1 text-sm">Address</label>
                        <input type="text" name="address" id="updateClientAddress" required
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Enter Client Full Address">
                    </div>

                    <div class="flex justify-end w-full">
                        <button type="button"
                            class="close mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm cursor-pointer">Cancel</button>
                        <button type="submit" name="update_client" id="updateButton"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm cursor-pointer">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center"
            style="background-color: rgba(0,0,0,0.4);">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <div class="flex flex-row items-center mb-4">
                    <i class="fa-solid fa-circle-exclamation mr-2" style="color: #c00707;"></i>
                    <h3 class="font-semibold text-lg">Delete Client Account</h3>
                </div>
                <p id="deleteMessage" class="mb-4 text-sm text-gray-600">
                    Are you sure you want to delete this client?
                </p>
                <div class="flex justify-end space-x-2">
                    <button class="close px-3 py-2 bg-gray-300 rounded hover:bg-gray-400 text-xs cursor-pointer">Cancel</button>
                    <a id="confirmDeleteBtn" href="#"
                        class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-xs">
                        Delete
                    </a>
                </div>
            </div>
        </div>

        <div id="addPetModal" class="modal fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 hidden"
            style="background-color: rgba(0,0,0,0.4);">
            <div class="bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-m font-semibold">Add new Pet</h3>
                        <h4 class="text-sm text-gray-600">Link this pet to the selected client</h4>
                    </div>
                    <button class="close text-xl cursor-pointer">&times;</button>
                </div>
                <form id="addPetForm" class="flex flex-wrap items-center justify-between" method="POST"
                    action="client-management.php">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Pet Name</label>
                        <input type="text" name="name"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Pet Name" required>
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Species</label>
                        <select
                            class="w-44 border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            name="species" required>
                            <option value="" disabled selected>Select Species</option>
                            <option value="Dog">Dog</option>
                            <option value="Cat">Cat</option>
                            <option value="Bird">Bird</option>
                            <option value="Rabbit">Rabbit</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="flex flex-row space-x-2">
                        <div class="mb-4 w-auto">
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Breed</label>
                            <input type="text" name="breed"
                                class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Breed">
                        </div>

                        <div class="mb-4 w-auto">
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Age</label>
                            <input type="number" name="age"
                                class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Years" min="0">
                        </div>

                        <div class="mb-4 w-auto">
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Gender</label>
                            <select
                                class="w-auto border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                name="gender" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>


                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Weight</label>
                        <input type="text" name="weight"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="e.g. 10kg">
                    </div>


                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Color</label>
                        <input type="text" name="color"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Pet Color">
                    </div>

                    <input type="hidden" name="owner_id" id="modal_owner_id">

                    <div class="mb-4 w-full">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Notes</label>
                        <textarea name="notes"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Any special notes about the pet"></textarea>
                    </div>

                    <div class="flex justify-end w-full">
                        <button type="button"
                            class="close mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm cursor-pointer">Cancel</button>
                        <button type="submit" name="add_pet" id="addPet"
                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700 text-sm cursor-pointer">Add
                            Pet</button>
                    </div>
                </form>
            </div>
        </div>


        <div id="updatePetModal"
            class="modal fixed inset-0 bg-black bg-opacity-40 items-center justify-center hidden z-[9999]"
            style="background-color: rgba(0,0,0,0.4);">
            <div class="bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-m font-semibold">Update Pet Information</h3>
                        <h4 class="text-sm text-gray-600">Edit the details of the selected Pet</h4>
                    </div>
                    <button class="close text-xl cursor-pointer">&times;</button>
                </div>
                <form id="updatePetForm" class="flex flex-wrap items-center justify-between" method="POST"
                    action="client-management.php">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="pet_id" id="updatePetId">
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Pet Name</label>
                        <input type="text" name="name" id="updatePetName"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Pet Name" required>
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Species</label>
                        <select
                            class="w-44 border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            name="species" id="updatePetSpecies" required>
                            <option value="" disabled selected>Select Species</option>
                            <option value="Dog">Dog</option>
                            <option value="Cat">Cat</option>
                            <option value="Bird">Bird</option>
                            <option value="Rabbit">Rabbit</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="flex flex-row space-x-2">
                        <div class="mb-4 w-auto">
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Breed</label>
                            <input type="text" name="breed" id="updatePetBreed"
                                class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Breed">
                        </div>

                        <div class="mb-4 w-auto">
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Age</label>
                            <input type="number" name="age" id="updatePetAge"
                                class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Years" min="0">
                        </div>

                        <div class="mb-4 w-auto">
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Gender</label>
                            <select
                                class="w-auto border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                name="gender" id="updatePetGender" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Weight</label>
                        <input type="text" name="weight" id="updatePetWeight"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="e.g. 10kg">
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Color</label>
                        <input type="text" name="color" id="updatePetColor"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Pet Color">
                    </div>

                    <div class="mb-4 w-full">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Notes</label>
                        <textarea name="notes" id="updatePetNotes"
                            class="w-full border border-gray-300 bg-white rounded px-2 py-1 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Any special notes about the pet"></textarea>
                    </div>

                    <div class="flex justify-end w-full">
                        <button type="button"
                            class="close mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm cursor-pointer">Cancel</button>
                        <button type="submit" name="update_pet" id="submitButton"
                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700 text-sm cursor-pointer">Update
                            Pet</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="deletePetModal"
            class="modal hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-[9999]"
            style="background-color: rgba(0,0,0,0.4);">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <div class="flex flex-row items-center mb-4">
                    <i class="fa-solid fa-circle-exclamation mr-2" style="color: #c00707;"></i>
                    <h3 class="font-semibold text-lg">Delete Pet Account</h3>
                </div>
                <p id="deletePetMessage" class="mb-4 text-sm text-gray-600">
                    Are you sure you want to delete this Pet?
                </p>
                <div class="flex justify-end space-x-2">
                    <button class="close px-3 py-2 bg-gray-300 rounded hover:bg-gray-400 text-xs cursor-pointer">Cancel</button>
                    <a id="confirmDeleteBtnPet" href="#"
                        class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-xs">
                        Delete
                    </a>
                </div>
            </div>
        </div>

        <div id="viewPetModal"
            class="modal fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 hidden"
            style="background-color: rgba(0,0,0,0.4);">
            <div
                class="custom-scrollbar bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative max-h-[70vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-m font-semibold">Pet Details</h3>
                        <h4 class="text-sm text-gray-600">All pets linked to this client</h4>
                    </div>
                    <button class="close text-xl cursor-pointer">&times;</button>
                </div>
                <div id="petDetailsContent" class="space-y-4"></div>
            </div>
        </div>

        <?php include '../includes/message-modal.php' ?>

    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // ===================== GLOBAL VARIABLES =====================
            const clientDetails = document.getElementById("clientDetails");
            const additionalDetails = document.getElementById("additionalDetails");
            const clientNameSpan = document.getElementById("clientName");
            const tableBody = document.getElementById("clientsBody");
            const pagination = document.getElementById("pagination");
            const rows = tableBody.querySelectorAll("tr");
            const rowsPerPage = 5;
            let currentPage = 1;
            let csrfToken = "<?= $csrf_token ?>";
            const totalPages = Math.ceil(rows.length / rowsPerPage);

            // ===================== HELPERS =====================
            const addField = (container, label, value) => {
                const p = document.createElement("p");
                p.innerHTML = `<span class="font-semibold">${label}:</span> ${value || "N/A"}`;
                container.appendChild(p);
            };

            const openModal = (modal) => {
                modal.classList.remove("hidden");
                modal.classList.add("flex");
                updateBodyScroll();
            };

            const closeModal = (modal) => {
                modal.classList.add("hidden");
                updateBodyScroll();
            };

            function showMessage(title, message, type = "success", callback = null) {
                const modal = document.getElementById("messageModal");
                const titleElement = document.getElementById("messageTitle");
                const textElement = document.getElementById("messageText");

                titleElement.textContent = title;
                textElement.textContent = message;

                titleElement.classList.toggle("text-green-600", type === "success");
                titleElement.classList.toggle("text-red-600", type !== "success");

                openModal(modal);
                modal.classList.add('flex');

                document.getElementById("closeMessageBtn").onclick = () => {
                    closeModal(modal);
                    if (callback) callback();
                };
            }

            // ===================== ADD CLIENT =====================
            document.getElementById("addClientForm").addEventListener("submit", function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append("csrf_token", csrfToken); // ✅ include CSRF token

                const addClient = document.getElementById('addClient');
                addClient.disabled = true;
                addClient.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

                fetch('../php/Add/add-client.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.csrf_token) csrfToken = data.csrf_token; // ✅ update token if refreshed
                        if (data.status === "success") {
                            showMessage("Success", data.message, "success", () => location.reload());
                        } else {
                            showMessage("Error", data.message, "error");
                        }
                    })
                    .catch(() => showMessage("Error", "Create failed."))
                    .finally(() => {
                        addClient.disabled = false;
                        addClient.innerHTML = "Create Account";
                    });
            });

            // ===================== CLIENT MODALS =====================
            document.querySelectorAll(".open-modal").forEach(btn => {
                btn.addEventListener("click", () => {
                    const modal = document.getElementById(btn.dataset.modal);
                    clientDetails.innerHTML = "<h3 class='font-semibold mb-4'>Contact information</h3>";
                    additionalDetails.innerHTML = "<h4 class='font-semibold mb-4'>Additional information</h4>";
                    clientNameSpan.textContent = btn.dataset.name;

                    addField(clientDetails, "Name", btn.dataset.name);
                    addField(clientDetails, "Email", btn.dataset.email);
                    addField(clientDetails, "Phone", btn.dataset.phone);
                    addField(clientDetails, "Emergency Contact", btn.dataset.emergency || "None");
                    addField(clientDetails, "Address", btn.dataset.address);

                    addField(additionalDetails, "Join Date", btn.dataset.created);
                    addField(additionalDetails, "Pet Count", btn.dataset.petcount);

                    openModal(modal);
                });
            });

            document.querySelectorAll(".open-update-modal").forEach(btn => {
                btn.addEventListener("click", () => {
                    fetch(`../Get/get-owner.php?id=${btn.dataset.id}`)
                        .then(res => res.json())
                        .then(client => {
                            document.getElementById("updateClientId").value = client.id;
                            document.getElementById("updateClientName").value = client.name || "";
                            document.getElementById("updateClientEmail").value = client.email || "";
                            document.getElementById("updateClientPassword").value = ""; // blank
                            document.getElementById("updateClientPhone").value = client.phone || "";
                            document.getElementById("updateClientEmergency").value = client.emergency || "";
                            document.getElementById("updateClientAddress").value = client.address || "";

                        });

                    openModal(document.getElementById("updateClientModal"));
                });
            });

            document.getElementById("updateClientForm").addEventListener("submit", function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append("csrf_token", csrfToken); // ✅ include CSRF token

                const updateButton = document.getElementById('updateButton');
                updateButton.disabled = true;
                updateButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

                fetch('../php/Update/update-client.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.csrf_token) csrfToken = data.csrf_token; // ✅ update token if refreshed
                        if (data.status === "success") {
                            showMessage("Success", data.message, "success", () => location.reload());
                        } else {
                            showMessage("Error", data.message, "error");
                        }
                    })
                    .catch(() => showMessage("Error", "Update failed."))
                    .finally(() => {
                        updateButton.disabled = false;
                        updateButton.innerHTML = "Update";
                    });
            });

            document.querySelectorAll(".open-pet-modal").forEach(btn => {
                btn.addEventListener("click", () => {
                    document.getElementById("modal_owner_id").value = btn.dataset.owner;
                    openModal(document.getElementById("addPetModal"));
                });
            });

            // ===================== ADD PET =====================
            document.getElementById("addPetForm").addEventListener("submit", function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append("csrf_token", csrfToken); // ✅ include CSRF token

                const addPet = document.getElementById('addPet');
                addPet.disabled = true;
                addPet.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

                fetch('../php/Add/add-pet.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.csrf_token) csrfToken = data.csrf_token; // ✅ update token if refreshed
                        if (data.status === "success") {
                            showMessage("Success", data.message, "success", () => location.reload());
                        } else {
                            showMessage("Error", data.message, "error");
                        }
                    })
                    .catch(() => showMessage("Error", "Add failed."))
                    .finally(() => {
                        addPet.disabled = false;
                        addPet.innerHTML = "Add Pet";
                    });
            });

            // ===================== PET MODAL (VIEW/EDIT/DELETE) =====================
            document.querySelectorAll('.open-modal[data-modal="viewPetModal"]').forEach(btn => {
                btn.addEventListener("click", () => {
                    const ownerId = btn.dataset.owner;
                    const modal = document.getElementById("viewPetModal");
                    const content = document.getElementById("petDetailsContent");

                    content.innerHTML = `
                <div class="flex justify-center items-center h-32">
                    <p class="text-gray-500 animate-pulse">Loading pets...</p>
                </div>
                `;

                    fetch("../helpers/fetch-pet.php?owner_id=" + ownerId)
                        .then(res => res.json())
                        .then(pets => {
                            if (!pets.length) {
                                content.innerHTML = `
                            <div class="text-center text-gray-500 py-6">
                                <i class="fa-solid fa-paw text-4xl mb-2 text-green-400"></i>
                                <p>No pets found for this client.</p>
                            </div>
                        `;
                                return;
                            }

                            content.innerHTML = "";
                            pets.forEach(pet => {
                                const card = document.createElement("div");
                                card.className = "mb-4 bg-white border border-green-200 rounded-xl p-5 shadow-md hover:shadow-lg transition";

                                card.innerHTML = `
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-bold text-xl text-green-700 flex items-center gap-2">
                                    <i class="fa-solid fa-paw text-green-500"></i> ${pet.name}
                                </h4>
                                <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full">
                                    ${pet.species || "Unknown"}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm text-gray-700">
                                <p><span class="font-semibold">Breed:</span> ${pet.breed || "N/A"}</p>
                                <p><span class="font-semibold">Age:</span> ${pet.age || "N/A"} yrs</p>
                                <p><span class="font-semibold">Gender:</span> ${pet.gender || "N/A"}</p>
                                <p><span class="font-semibold">Weight:</span> ${pet.weight || "N/A"}</p>
                                <p><span class="font-semibold">Color:</span> ${pet.color || "N/A"}</p>
                            </div>
                            <p class="mt-2 text-gray-600 text-sm">
                                <span class="font-semibold">Notes:</span> ${pet.notes || "None"}
                            </p>
                            <div class="flex justify-end gap-2 mt-4">
                                <button class="edit-pet-btn cursor-pointer flex items-center gap-1 text-green-700 text-sm font-semibold px-3 py-1.5 bg-green-100 rounded-lg hover:bg-green-600 hover:text-white transition" data-id="${pet.id}">
                                    <i class="fa-solid fa-pen-alt"></i> Edit
                                </button>
                                <button class="delete-pet-btn cursor-pointer flex items-center gap-1 text-red-600 text-sm font-semibold px-3 py-1.5 bg-red-100 rounded-lg hover:bg-red-500 hover:text-white transition" data-id="${pet.id}" data-name="${pet.name}">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </div>
                        `;
                                content.append(card);

                                // Edit Pet
                                card.querySelector(".edit-pet-btn").addEventListener("click", () => {
                                    fetch(`../Get/get-pet.php?id=${pet.id}`)
                                        .then(res => res.json())
                                        .then(data => {
                                            document.getElementById("updatePetId").value = data.id;
                                            document.getElementById("updatePetName").value = data.name;
                                            document.getElementById("updatePetSpecies").value = data.species;
                                            document.getElementById("updatePetBreed").value = data.breed || "";
                                            document.getElementById("updatePetAge").value = data.age || "";
                                            document.getElementById("updatePetGender").value = data.gender;
                                            document.getElementById("updatePetWeight").value = data.weight || "";
                                            document.getElementById("updatePetColor").value = data.color || "";
                                            document.getElementById("updatePetNotes").value = data.notes || "";
                                        });
                                    openModal(document.getElementById("updatePetModal"));
                                });

                                // Delete Pet
                                card.querySelector(".delete-pet-btn").addEventListener("click", () => {
                                    const modal = document.getElementById("deletePetModal");
                                    document.getElementById("deletePetMessage").textContent =
                                        `Are you sure you want to delete "${pet.name}"?`;
                                    document.getElementById("confirmDeleteBtnPet").dataset.id = pet.id;
                                    openModal(modal);
                                });
                            });
                        })
                        .catch(() => content.innerHTML = "<p class='text-red-500'>Failed to load pet data.</p>");

                    openModal(modal);
                });
            });

            document.getElementById("updatePetForm").addEventListener("submit", function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append("csrf_token", csrfToken); // ✅ include CSRF token

                const submitButton = document.getElementById('submitButton');
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

                fetch('../php/Update/update-pet.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.csrf_token) csrfToken = data.csrf_token; // ✅ update token if refreshed
                        if (data.status === "success") {
                            showMessage("Success", data.message, "success", () => location.reload());
                        } else {
                            showMessage("Error", data.message, "error");
                        }
                    })
                    .catch(() => showMessage("Error", "Update failed."))
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.innerHTML = "Update";
                    });
            });


            // Confirm Delete Pet
            document.getElementById("confirmDeleteBtnPet").addEventListener("click", e => {
                e.preventDefault();
                const btn = e.target;
                const petId = btn.dataset.id;

                btn.textContent = "Deleting...";
                btn.disabled = true;

                fetch("../php/Delete/delete-pet.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({ pet_id: petId, csrf_token: csrfToken })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.csrf_token) csrfToken = data.csrf_token;
                        showMessage(
                            data.status === "success" ? "Success" : "Error",
                            data.message,
                            data.status,
                            () => location.reload()
                        );
                    })
                    .catch(() => showMessage("Error", "Delete failed."))
                    .finally(() => {
                        btn.textContent = "Delete";
                        btn.disabled = false;
                    });
            });

            // ===================== SEARCH & PAGINATION =====================
            document.getElementById("search").addEventListener("input", function () {
                const term = this.value.toLowerCase();
                rows.forEach(row => {
                    const name = row.cells[0].textContent.toLowerCase();
                    const contact = row.cells[1].textContent.toLowerCase();
                    row.style.display = (name.includes(term) || contact.includes(term)) ? "" : "none";
                });
            });

            const showPage = page => {
                currentPage = page;
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                rows.forEach((row, i) => row.style.display = (i >= start && i < end) ? "" : "none");
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
                    btn.textContent = i;
                    btn.className = i === currentPage ? "bg-green-500 text-xs text-white py-1 px-2 rounded-lg" : "bg-gray-200 text-xs py-1 px-2 rounded-lg";
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

            // ===================== DELETE CLIENT =====================
            document.querySelectorAll(".open-delete-modal").forEach(btn => {
                btn.addEventListener("click", () => {
                    const modal = document.getElementById("deleteModal");
                    const clientName = btn.dataset.name;
                    const clientId = btn.dataset.id;

                    document.getElementById("deleteMessage").textContent =
                        `Are you sure you want to delete "${clientName}" and their account? This will remove all pets and records.`;
                    document.getElementById("confirmDeleteBtn").dataset.id = clientId;

                    openModal(modal);
                });
            });

            // Confirm Delete Client
            document.getElementById("confirmDeleteBtn").addEventListener("click", e => {
                e.preventDefault();
                const btn = e.target;
                const clientID = btn.dataset.id;

                btn.textContent = "Deleting...";
                btn.disabled = true;

                fetch("../php/Delete/delete-client.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({ client_id: clientID, csrf_token: csrfToken })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.csrf_token) csrfToken = data.csrf_token;
                        showMessage(
                            data.status === "success" ? "Success" : "Error",
                            data.message,
                            data.status,
                            () => location.reload()
                        );
                    })
                    .catch(() => showMessage("Error", "Delete failed."))
                    .finally(() => {
                        btn.textContent = "Delete";
                        btn.disabled = false;
                    });
            });


            // ===================== GENERIC MODAL CLOSE =====================
            document.querySelectorAll(".modal .close").forEach(btn => {
                btn.addEventListener("click", () => closeModal(btn.closest(".modal")));
            });
            document.querySelectorAll(".modal").forEach(modal => {
                modal.addEventListener("click", e => { if (e.target === modal) closeModal(modal); });
            });
        });
    </script>

</body>

</html>