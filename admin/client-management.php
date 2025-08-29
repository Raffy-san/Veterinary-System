<?php
include_once '../config/config.php';
require_once '../functions/session.php';
require_once '../helpers/fetch.php';
require_once '../functions/crud.php';
SessionManager::requireLogin();
SessionManager::requireRole('admin');

$admin = SessionManager::getUser($pdo);

if (!$admin) {
    SessionManager::logout('../login.php');
}

if (isset($_GET['delete_id'])) {
    $user_id = intval($_GET['delete_id']);
    if (deleteClient($pdo, $user_id)) {
        header("Location: client-management.php?deleted=1");
        exit;
    } else {
        header("Location: client-management.php?deleted=0");
        exit;
    }
}

if (isset($_GET['delete_pet_id'])) {
    $pet_id = intval($_GET['delete_pet_id']);
    if (deletePet($pdo, $pet_id)) {
        header("Location: client-management.php?pet_deleted=1");
        exit;
    } else {
        header("Location: client-management.php?error=not_found");
        exit;
    }
}


if (isset($_POST['submit'])) {
    $data = [
        'name' => $_POST['name'],
        'username' => $_POST['username'],
        'password' => $_POST['password'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'emergency' => $_POST['emergency'],
        'address' => $_POST['address']
    ];

    if (addClient($pdo, $data)) {
        header("Location: client-management.php?added=1");
        exit;
    } else {
        header("Location: client-management.php?added=0");
        exit;
    }
}

if (isset($_POST['update_client'])) {
    $data = [
        'owner_id' => $_POST['owner_id'],
        'name' => $_POST['name'],
        'username' => $_POST['username'],
        'password' => $_POST['password'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'emergency_contact' => $_POST['emergency'],
        'address' => $_POST['address']
    ];

    if (updateClient($pdo, $data)) {
        header("Location: client-management.php?updated=1");
        exit;
    } else {
        header("Location: client-management.php?updated=0");
        exit;
    }
}

if (isset($_POST['add_pet'])) {
    $data = [
        'name' => $_POST['name'],
        'species' => $_POST['species'],
        'breed' => $_POST['breed'],
        'age' => $_POST['age'],
        'gender' => $_POST['gender'],
        'weight' => $_POST['weight'],
        'color' => $_POST['color'],
        'owner_id' => $_POST['owner_id'],
        'notes' => $_POST['notes']
    ];

    if (addPet($pdo, $data)) {
        header("Location: client-management.php?pet_added=1");
        exit;
    } else {
        header("Location: client-management.php?pet_added=0");
        exit;
    }
}

if (isset($_POST['update_pet'])) {
    $data = [
        'pet_id' => $_POST['pet_id'],
        'name' => $_POST['name'],
        'species' => $_POST['species'],
        'breed' => $_POST['breed'],
        'age' => $_POST['age'],
        'gender' => $_POST['gender'],
        'weight' => $_POST['weight'],
        'color' => $_POST['color'],
        'notes' => $_POST['notes']
    ];

    if (updatePet($pdo, $data)) {
        header("Location: client-management.php?pet_updated=1");
        exit;
    } else {
        header("Location: client-management.php?pet_updated=0");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/green-paw.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/script.js"></script>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Client Management</title>
</head>

<body class="bg-green-100 w-full h-screen overflow-y-auto">
    <?php include_once '../includes/admin-header.php'; ?>

    <main class="p-10">
        <!-- Main Client Table -->
        <section class="p-10 bg-white rounded-lg shadow-md">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-lg">Client Management</h3>
                    <h4 class="text-gray-600">Manage client information and accounts</h4>
                </div>
                <button data-modal="addClientModal"
                    class="open-modal mt-4 px-4 py-2 bg-green-600 text-white rounded-lg text-xs hover:bg-green-700">
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
                    <tr class="text-sm text-left border-b">
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

                        echo '<tr class="border-b hover:bg-green-50 text-sm text-left">';
                        echo '<td class="py-2">' . htmlspecialchars($client['name']) . '</td>';
                        echo '<td class="py-2 flex flex-col">' . '<span><i class="fa-solid fa-envelope text-green-600"></i>&nbsp;' . htmlspecialchars($client['email']) . '</span>' . '<span class="text-gray-500 text-xs"><i class="fa-solid fa-phone">&nbsp;</i>' . htmlspecialchars($client['phone']) . '</span></td>';
                        echo '<td class="py-2">' . htmlspecialchars($client['created_at']) . '</td>';
                        echo '<td class="py-2"><span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">' . $client['pet_count'] . '</span></td>';
                        echo '<td class="py-2 text-right">
                                <button 
                                    data-modal="viewModal" 
                                    data-id="' . $client['owner_id'] . '"
                                   data-name="' . htmlspecialchars($client['name'] ?? '') . '"
                                    data-email="' . htmlspecialchars($client['email'] ?? '') . '"
                                    data-phone="' . htmlspecialchars($client['phone'] ?? '') . '"
                                    data-emergency="' . htmlspecialchars($client['emergency'] ?? '') . '"
                                    data-created="' . htmlspecialchars($client['created_at'] ?? '') . '"
                                    data-address="' . htmlspecialchars($client['address'] ?? '') . '"
                                    data-petcount="' . $client['pet_count'] . '"
                                    data-pets="' . htmlspecialchars($client['pets'] ?? '') . '"
                                    class="open-modal fa-solid fa-eye text-gray-700 mr-2 bg-green-100 p-1.5 border rounded border-green-200 hover:bg-green-300">
                                </button>
                                <button class="open-update-modal fa-solid fa-pencil-alt text-gray-700 mr-2 bg-green-100 p-1.5 rounded border border-green-200 hover:bg-green-300" data-id="' . $client['owner_id'] . '"></button>
                                <button 
                                data-owner="' . $client['owner_id'] . '" 
                                class="open-pet-modal text-gray-700 mr-2 bg-green-100 text-xs font-semibold p-1.5 rounded border border-green-200 hover:bg-green-300">
                                <i class="fa-solid fa-plus mr-1"></i>Add Pet
                                </button>
                                 <button
                                    data-modal="viewPetModal"
                                    data-owner="' . $client['owner_id'] . '"
                                 class="open-modal text-gray-700 mr-2 bg-green-100 text-xs font-semibold p-1.5 rounded border border-green-200 hover:bg-green-300">View Pet</button>
                                <button 
                                    class="open-delete-modal fa-solid fa-trash text-gray-700 mr-2 bg-green-100 p-1.5 border rounded border-green-200 hover:bg-red-400"
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
            class="modal fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div class="bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-m font-semibold">Create Client Account</h3>
                        <h4 class="text-sm text-gray-600">Add new client to the system</h4>
                    </div>
                    <button class="close text-xl" aria-label="Close">&times;</button>
                </div>
                <form class="flex flex-wrap items-center justify-between" method="POST" action="client-management.php">
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
                        <input type="tel" name="emergency" pattern="^09\d{9}$"
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
                        <button type="submit" name="submit"
                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700 text-sm">Create
                            Account</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Modal -->
        <div id="viewModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="custom-scrollbar bg-green-100 rounded-lg p-4 max-h-[60vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-m">Client Details - <span id="clientName"></span></h3>
                        <h4 class="text-gray-500 text-sm">Complete client and pet information</h4>
                    </div>
                    <button class="close text-xl" aria-label="Close">&times;</button>
                </div>
                <div class="flex flex-row justify-between space-x-2">
                    <div id="clientDetails" class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4 max-w-[200px]">
                        <!-- Client details will be populated here -->
                    </div>
                    <div id="additionalDetails"
                        class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4 max-w-[200px]">
                        <!-- Additional details will be populated here -->
                    </div>
                </div>
                <div id="petDetails" class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4 min-w-[230px]">
                    <!-- Pet details will be populated here -->
                </div>
            </div>
        </div>

        <div id="updateClientModal"
            class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-m">Update Client Information</h3>
                        <h4 class="text-gray-500 text-sm">Edit the details of the selected client</h4>
                    </div>
                    <button class="close text-xl" aria-label="Close">&times;</button>
                </div>
                <form class="flex flex-wrap items-center justify-between" id="updateClientForm" method="POST"
                    action="client-management.php">
                    <!-- Owner ID -->
                    <input type="hidden" name="owner_id" id="updateClientId">

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Name</label>
                        <input type="text" name="name" id="updateClientName" required
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Client Name">
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Username</label>
                        <input type="text" name="username" id="updateClientUsername" required
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Client Username">
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Password</label>
                        <input type="password" name="password" id="updateClientPassword"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Leave blank to keep current password">
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Email</label>
                        <input type="email" name="email" id="updateClientEmail"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Client Email">
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Phone</label>
                        <input type="tel" name="phone" id="updateClientPhone" required pattern="^09\d{9}$"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="09XXXXXXXXX">
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm">Emergency Contact</label>
                        <input type="tel" name="emergency" id="updateClientEmergency" pattern="^09\d{9}$"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="09XXXXXXXXX">
                    </div>

                    <div class="mb-4 w-full">
                        <label class="block text-gray-700 mb-1 text-sm">Address</label>
                        <input type="text" name="address" id="updateClientAddress" required
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Enter Client Full Address">
                    </div>

                    <div class="flex justify-end w-full">
                        <button type="button"
                            class="close mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">Cancel</button>
                        <button type="submit" name="update_client"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">Update</button>
                    </div>
                </form>
            </div>
        </div>


        <!-- Delete Confirmation Modal -->
        <div id="deleteModal"
            class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <div class="flex flex-row items-center mb-4">
                    <i class="fa-solid fa-circle-exclamation mr-2" style="color: #c00707;"></i>
                    <h3 class="font-semibold text-lg">Delete Client Account</h3>
                </div>
                <p id="deleteMessage" class="mb-4 text-sm text-gray-600">
                    Are you sure you want to delete this client?
                </p>
                <div class="flex justify-end space-x-2">
                    <button class="close px-3 py-2 bg-gray-300 rounded hover:bg-gray-400 text-xs">Cancel</button>
                    <a id="confirmDeleteBtn" href="#"
                        class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-xs">
                        Delete
                    </a>
                </div>
            </div>
        </div>

        <div id="addPetModal"
            class="modal fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div class="bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-m font-semibold">Add new Pet</h3>
                        <h4 class="text-sm text-gray-600">Link this pet to the selected client</h4>
                    </div>
                    <button class="close text-xl">&times;</button>
                </div>
                <form class="flex flex-wrap items-center justify-between" method="POST" action="client-management.php">
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Pet Name</label>
                        <input type="text" name="name"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Pet Name" required>
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Species</label>
                        <select
                            class="w-44 border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
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
                                class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Breed">
                        </div>

                        <div class="mb-4 w-auto">
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Age</label>
                            <input type="number" name="age"
                                class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Years">
                        </div>

                        <div class="mb-4 w-auto">
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Gender</label>
                            <select
                                class="w-auto border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
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
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="e.g. 10kg">
                    </div>


                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Color</label>
                        <input type="text" name="color"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Pet Color">
                    </div>

                    <input type="hidden" name="owner_id" id="modal_owner_id">

                    <div class="mb-4 w-full">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Notes</label>
                        <textarea name="notes"
                            class="w-full border rounded px-2 py-1 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Any special notes about the pet"></textarea>
                    </div>

                    <div class="flex justify-end w-full">
                        <button type="button"
                            class="close mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">Cancel</button>
                        <button type="submit" name="add_pet"
                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700 text-sm">Add
                            Pet</button>
                    </div>
                </form>
            </div>
        </div>


        <div id="updatePetModal"
            class="modal fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden z-[9999]">
            <div class="bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-m font-semibold">Update Pet Information</h3>
                        <h4 class="text-sm text-gray-600">Edit the details of the selected Pet</h4>
                    </div>
                    <button class="close text-xl">&times;</button>
                </div>
                <form class="flex flex-wrap items-center justify-between" method="POST" action="client-management.php">
                    <input type="hidden" name="pet_id" id="updatePetId">
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Pet Name</label>
                        <input type="text" name="name" id="updatePetName"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Pet Name" required>
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Species</label>
                        <select
                            class="w-44 border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
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
                                class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Breed">
                        </div>

                        <div class="mb-4 w-auto">
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Age</label>
                            <input type="number" name="age" id="updatePetAge"
                                class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Years">
                        </div>

                        <div class="mb-4 w-auto">
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Gender</label>
                            <select
                                class="w-auto border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                name="gender" id="updatePetGender" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Weight</label>
                        <input type="text" name="weight" id="updatePetWeight"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="e.g. 10kg">
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Color</label>
                        <input type="text" name="color" id="updatePetColor"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Pet Color">
                    </div>

                    <div class="mb-4 w-full">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Notes</label>
                        <textarea name="notes" id="updatePetNotes"
                            class="w-full border rounded px-2 py-1 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Any special notes about the pet"></textarea>
                    </div>

                    <div class="flex justify-end w-full">
                        <button type="button"
                            class="close mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">Cancel</button>
                        <button type="submit" name="update_pet"
                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700 text-sm">Update
                            Pet</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="deletePetModal"
            class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <div class="flex flex-row items-center mb-4">
                    <i class="fa-solid fa-circle-exclamation mr-2" style="color: #c00707;"></i>
                    <h3 class="font-semibold text-lg">Delete Pet Account</h3>
                </div>
                <p id="deletePetMessage" class="mb-4 text-sm text-gray-600">
                    Are you sure you want to delete this Pet?
                </p>
                <div class="flex justify-end space-x-2">
                    <button class="close px-3 py-2 bg-gray-300 rounded hover:bg-gray-400 text-xs">Cancel</button>
                    <a id="confirmDeleteBtnPet" href="#"
                        class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-xs">
                        Delete
                    </a>
                </div>
            </div>
        </div>

        <div id="viewPetModal"
            class="modal fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div
                class="custom-scrollbar bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative max-h-[70vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-m font-semibold">Patient Details</h3>
                        <h4 class="text-sm text-gray-600">All pets linked to this client</h4>
                    </div>
                    <button class="close text-xl">&times;</button>
                </div>
                <div id="petDetailsContent" class="space-y-4"></div>
            </div>
        </div>


    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const clientDetails = document.getElementById("clientDetails");
            const additionalDetails = document.getElementById("additionalDetails");
            const petDetails = document.getElementById("petDetails");
            const clientNameSpan = document.getElementById("clientName");
            const tableBody = document.getElementById("clientsBody");
            const rows = tableBody.querySelectorAll("tr");
            const pagination = document.getElementById("pagination");
            const rowsPerPage = 6;
            let currentPage = 1;
            const totalPages = Math.ceil(rows.length / rowsPerPage);


            // Utility to add fields
            const addField = (container, label, value) => {
                const p = document.createElement("p");
                p.innerHTML = `<span class="font-semibold">${label}:</span> ${value || "N/A"}`;
                container.appendChild(p);
            };

            // Open View Modal
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

                    fetch(`../helpers/fetch-pet.php?owner_id=${btn.dataset.id}`)
                        .then(res => res.json())
                        .then(pets => {
                            petDetails.innerHTML = "<h3 class='font-semibold font-sm mb-4'>Registered Pets</h3>";
                            if (pets.length === 0) {
                                petDetails.innerHTML = "<p class='font-semibold'>No pets found</p>";
                            } else {
                                pets.forEach((pet, i) => {
                                    const petDiv = document.createElement("div");
                                    petDiv.className = "mb-2 p-2 border rounded bg-green-50";
                                    petDiv.innerHTML = `
                                      <div class="bg-white border border-green-300 rounded-lg shadow-sm p-3">
                                        <div class="flex flex-row mb-2 space-x-2">
                                            <h4 class="font-bold">${pet.name}</h4>
                                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">
                                                ${pet.species || "Unknown"}
                                            </span>
                                        </div>
                                        <ul class="text-sm space-x-2 text-gray-500 flex flex-row">
                                            <li>${pet.breed || "N/A"}</li>
                                            <li>•</li>
                                            <li>${pet.age || "N/A"}<span>&nbspYears</span></li>
                                            <li>•</li>
                                            <li>${pet.gender || "N/A"}</li>
                                        </ul>
                                        <span class="text-sm text-gray-500">Registered:&nbsp${pet.registered_at}</span>
                                    </div>
                                `;
                                    petDetails.appendChild(petDiv);
                                });
                            }
                        })

                    modal.classList.remove("hidden");
                    updateBodyScroll();
                });
            });

            document.querySelectorAll(".open-update-modal").forEach(btn => {
                btn.addEventListener("click", () => {
                    const modal = document.getElementById("updateClientModal");
                    const clientId = btn.dataset.id;

                    fetch(`../Get/get-owner.php?id=${clientId}`)
                        .then(res => res.json())
                        .then(client => {
                            document.getElementById("updateClientId").value = client.id;         // Owner ID
                            document.getElementById("updateClientName").value = client.name;
                            document.getElementById("updateClientUsername").value = client.username;
                            document.getElementById("updateClientPassword").value = ''; // Leave blank
                            document.getElementById("updateClientEmail").value = client.email;
                            document.getElementById("updateClientPhone").value = client.phone;
                            document.getElementById("updateClientEmergency").value = client.emergency || '';
                            document.getElementById("updateClientAddress").value = client.address;
                        });


                    modal.classList.remove("hidden");
                    updateBodyScroll();
                });
            });

            document.querySelectorAll(".open-pet-modal").forEach(btn => {
                btn.addEventListener("click", () => {
                    const ownerId = btn.dataset.owner;
                    document.getElementById("modal_owner_id").value = ownerId;
                    document.getElementById("addPetModal").classList.remove("hidden");
                    updateBodyScroll();
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

            // Search functionality
            document.getElementById("search").addEventListener("input", function () {
                const term = this.value.toLowerCase();
                rows.forEach(row => {
                    const name = row.cells[0].textContent.toLowerCase();
                    const contact = row.cells[1].textContent.toLowerCase();
                    row.style.display = (name.includes(term) || contact.includes(term)) ? "" : "none";
                });
            });

            // Pagination
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
                    prev.className = "text-xs px-3 py-1 bg-gray-200 rounded";
                    prev.textContent = "Prev";
                    prev.onclick = () => showPage(currentPage - 1);
                    pagination.appendChild(prev);
                }
                for (let i = 1; i <= totalPages; i++) {
                    const btn = document.createElement("button");
                    btn.className = `text-xs px-3 py-1 rounded ${i === currentPage ? "bg-green-500 text-white" : "bg-gray-200"}`;
                    btn.textContent = i;
                    btn.onclick = () => showPage(i);
                    pagination.appendChild(btn);
                }
                if (currentPage < totalPages) {
                    const next = document.createElement("button");
                    next.className = "text-xs px-3 py-1 bg-gray-200 rounded";
                    next.textContent = "Next";
                    next.onclick = () => showPage(currentPage + 1);
                    pagination.appendChild(next);
                }
            };

            showPage(1);

            // Delete Modal Logic
            document.querySelectorAll(".open-delete-modal").forEach(btn => {
                btn.addEventListener("click", () => {
                    const modal = document.getElementById("deleteModal");
                    const clientName = btn.dataset.name;
                    const userId = btn.dataset.id;

                    document.getElementById("deleteMessage").textContent =
                        `Are you sure you want to delete "${clientName}" and their account? This action cannot be undone and will remove all associated pets and medical records.`;
                    document.getElementById("confirmDeleteBtn").href = `client-management.php?delete_id=${userId}`;

                    modal.classList.remove("hidden");
                    updateBodyScroll();
                });
            });
        });

        document.querySelectorAll('.open-modal[data-modal="viewPetModal"]').forEach(btn => {
            btn.addEventListener('click', function () {
                const ownerId = this.getAttribute('data-owner');
                const modal = document.getElementById('viewPetModal');
                const content = document.getElementById('petDetailsContent');

                content.innerHTML = '<p class="text-gray-500">Loading pets...</p>';

                fetch('../helpers/fetch-pet.php?owner_id=' + ownerId)
                    .then(response => response.json())
                    .then(pets => {
                        if (pets.length === 0) {
                            content.innerHTML = '<p class="text-gray-500">No pets found for this client.</p>';
                        } else {
                            content.innerHTML = '';
                            pets.forEach(pet => {
                                const petCard = document.createElement('div');
                                petCard.className = 'mb-4 bg-white border border-green-300 rounded-lg p-4 shadow-sm';
                                petCard.innerHTML = `
                            <h4 class="font-semibold text-lg text-green-700 mb-2">${pet.name}</h4>
                            <p><span class="font-semibold">Species:</span> ${pet.species || 'N/A'}</p>
                            <p><span class="font-semibold">Breed:</span> ${pet.breed || 'N/A'}</p>
                            <p><span class="font-semibold">Age:</span> ${pet.age || 'N/A'} years</p>
                            <p><span class="font-semibold">Gender:</span> ${pet.gender || 'N/A'}</p>
                            <p><span class="font-semibold">Weight:</span> ${pet.weight || 'N/A'}</p>
                            <p><span class="font-semibold">Color:</span> ${pet.color || 'N/A'}</p>
                            <p><span class="font-semibold">Notes:</span> ${pet.notes || 'None'}</p>
                            <div class="flex justify-end mt-2">
                            <button 
                                class="open-update-pet-modal font-semibold text-gray-700 text-sm mr-2 bg-green-100 p-1.5 border rounded border-green-200 hover:bg-green-600 hover:text-white"
                                data-id="${pet.id}">
                                <i class="fa-solid fa-pen-alt pr-2"></i>Edit
                            </button>
                           <button 
                                class="open-delete-pet-modal font-semibold text-gray-700 text-sm mr-2 bg-green-100 p-1.5 border rounded border-green-200 hover:bg-red-400"
                                data-name="${pet.name}" 
                                data-id="${pet.id}">
                                <i class="fa-solid fa-trash pr-2"></i>Delete
                            </button>
                            </div>
                        `;
                                // Attach delete listener **here**
                                petCard.querySelector('.open-delete-pet-modal').addEventListener('click', function () {
                                    const modal = document.getElementById("deletePetModal");
                                    const petName = this.dataset.name;
                                    const petId = this.dataset.id;

                                    document.getElementById("deletePetMessage").textContent =
                                        `Are you sure you want to delete "${petName}"? This action cannot be undone.`;
                                    document.getElementById("confirmDeleteBtnPet").href = `client-management.php?delete_pet_id=${petId}`;

                                    modal.classList.remove("hidden");
                                    updateBodyScroll();
                                });

                                content.appendChild(petCard);
                            });

                            document.querySelectorAll(".open-update-pet-modal").forEach(btn => {
                                btn.addEventListener("click", () => {
                                    const modal = document.getElementById("updatePetModal");
                                    const petId = btn.dataset.id;

                                    fetch(`../Get/get-pet.php?id=${petId}`)
                                        .then(res => res.json())
                                        .then(pet => {
                                            
                                            document.getElementById('updatePetId').value = pet.id;
                                            document.getElementById('updatePetName').value = pet.name;
                                            document.getElementById('updatePetSpecies').value = pet.species;
                                            document.getElementById('updatePetBreed').value = pet.breed || '';
                                            document.getElementById('updatePetAge').value = pet.age || '';
                                            document.getElementById('updatePetGender').value = pet.gender;
                                            document.getElementById('updatePetWeight').value = pet.weight || '';
                                            document.getElementById('updatePetColor').value = pet.color || '';
                                            document.getElementById('updatePetNotes').value = pet.notes || '';

                                        });

                                    modal.classList.remove("hidden");
                                    updateBodyScroll();
                                });
                            });
                        }
                    })
                    .catch(() => {
                        content.innerHTML = '<p class="text-red-500">Failed to load pet data.</p>';
                    });

                modal.classList.remove('hidden');
                updateBodyScroll();
            });
        });
    </script>
</body>

</html>