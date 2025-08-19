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
    <title>Pet Management</title>
</head>

<body class="bg-green-100 w-full h-screen overflow-y-auto">
    <?php
    include_once '../includes/admin-header.php';
    ?>
    <main class="p-10">
        <div id="addPetModal"
            class="modal fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div class="bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-m font-semibold">Add new Patient</h3>
                        <h4 class="text-sm text-gray-600">Enter the Pet and owner information below</h4>
                    </div>
                    <button class="close text-xl">&times;</button>
                </div>
                <form class="flex flex-wrap items-center justify-between" method="POST" action="add-pet.php">
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Pet Name</label>
                        <input type="text" name="petname"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Client Name" required>
                    </div>
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Pet Species</label>
                        <select
                            class="w-44 border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            name="petspecies" id="petspecies" required>
                            <option value="" selected disabled>Select Species</option>
                            <option value="Dog">Dog</option>
                            <option value="Cat">Cat</option>
                            <option value="Bird">Bird</option>
                            <option value="Rabbit">Rabbit</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Breed</label>
                        <input type="text" name="breed"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Enter Breed" required>
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Age</label>
                        <input type="number" name="age"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="e.g., 2 years">
                    </div>
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Gender</label>
                        <select
                            class="w-44 border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            name="gender" id="gender" required>
                            <option value="" disabled selected>Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="mb-4 w-auto relative">
                        <label for="owner_name" class="block text-gray-700 mb-1 text-sm font-semibold">Owner
                            Name</label>
                        <input type="text" name="owner_name" id="owner_name"
                            class="w-auto border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Start typing owner name..." autocomplete="off" required>
                        <div id="ownerSuggestions" class="bg-white border rounded shadow-md absolute hidden w-60 z-50">
                        </div>
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Phone</label>
                        <input type="tel" name="phone" id="phone"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="09XXXXXXXXX">
                    </div>

                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Email</label>
                        <input type="email" name="email" id="email"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Enter Owner Email">
                    </div>

                    <input type="hidden" name="owner_id" id="owner_id">

                    <div class="mb-4 w-full">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Notes</label>
                        <textarea name="notes"
                            class="w-full border rounded px-2 py-1 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Any special notes about the pet"></textarea>
                    </div>

                    <div class="flex justify-end w-full">
                        <button type="button"
                            class="close mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700 text-sm">Add
                            Pet</button>
                    </div>
                </form>
            </div>
        </div>
        <section class="p-10 bg-white rounded-lg shadow-md">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-lg">Pet Management</h3>
                    <h4 class="text-gray-600">Manage Pet informations</h4>
                </div>
                <button data-modal="addPetModal"
                    class="open-modal mt-4 px-4 py-2 bg-green-700 text-white rounded-lg text-xs hover:bg-green-700"><i
                        class="fa-solid fa-plus mr-2"></i>Add
                    Pet</button>
            </div>
            <div class="mb-4">
                <form>
                    <i class="fa-solid fa-search text-sm"></i>
                    <input type="search" id="search" placeholder="Search Pets..."
                        class="bg-gray-100 rounded px-3 py-2 mb-4 text-sm w-64">
                </form>
            </div>
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
                        <th class="text-right font-semibold ">Actions</th>
                    </tr>
                </thead>
                <tbody id="petsBody">
                    <?php
                    $pets = fetchAllData(
                        $pdo,
                        "SELECT p.id, 
                        p.name AS pet_name, 
                        p.species, 
                        p.breed, 
                        p.age, 
                        p.gender, 
                        o.name AS owner_name, 
                        o.phone AS owner_phone,  
                        o.email AS owner_email
                        FROM pets p
                        JOIN owners o ON p.owner_id = o.id"
                    );
                    foreach ($pets as $row) {
                        echo "<tr class='border-b hover:bg-green-50 text-sm text-left'>";
                        echo "<td class='py-2'>{$row['pet_name']}</td>";
                        echo "<td class='py-2 flex flex-col'>
                        <span>{$row['species']}</span>
                        <span class='text-gray-500'>{$row['breed']}</span>
                        </td>";
                        echo "<td class='py-2'>{$row['age']}<span>&nbspYears</span></td>";
                        echo "<td class='py-2'>{$row['gender']}</td>";
                        echo "<td class='py-2'>{$row['owner_name']}</td>";
                        echo "<td class='flex flex-col py-2'>
                            <span><i class='fa-solid fa-envelope text-green-600'></i>&nbsp{$row['owner_email']}</span>
                            <span class='text-gray-500 text-xs'><i class='fa-solid fa-phone'></i>&nbsp{$row['owner_phone']}</span>
                        </td>";
                        echo "<td class='text-center py-2'><span class='bg-green-500 text-white py-1 px-2 rounded text-xs font-semibold'>Active</span></td>";
                        echo "<td class='text-right py-2'>
                        <button class='fa-solid fa-eye text-gray-700 mr-2 bg-green-100 p-1.5 rounded border border-green-200 hover:bg-green-300'></button>
                        <button class='fa-solid fa-trash text-gray-700 mr-2 bg-green-100 p-1.5 rounded border border-green-200 hover:bg-green-300'></button>
                    </td>";
                        echo "</tr>";
                    }

                    ?>
                </tbody>
            </table>
        </section>
    </main>
    <script>
        const ownerInput = document.getElementById("owner_name");
        const suggestionsBox = document.getElementById("ownerSuggestions");
        const tableBody = document.getElementById("petsBody");
        const rows = tableBody.querySelectorAll("tr");
        // Open modal
        document.querySelectorAll(".open-modal").forEach(button => {
            button.addEventListener("click", () => {
                const modalId = button.dataset.modal;

                document.getElementById(modalId).classList.remove("hidden");
                document.body.style.overflow = "hidden"; // Prevent background scroll
            });
        });

        // Close modal when clicking on .close
        document.querySelectorAll(".modal .close").forEach(closeBtn => {
            closeBtn.addEventListener("click", () => {
                const modal = closeBtn.closest(".modal");
                modal.classList.add("hidden");
                document.body.style.overflow = "auto";

                const form = modal.querySelector("form");
                if (form) form.reset();
            });
        });

        // Close modal when clicking outside content
        document.querySelectorAll(".modal").forEach(modal => {
            modal.addEventListener("click", e => {
                if (e.target === modal) modal.classList.add("hidden");
            });
        });

        ownerInput.addEventListener("input", () => {
            suggestionsBox.style.width = ownerInput.offsetWidth + "px";

            let query = ownerInput.value;
            if (query.length < 2) {
                suggestionsBox.classList.add("hidden");
                return;
            }

            fetch("../helpers/fetch-owner.php?query=" + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    suggestionsBox.innerHTML = "";
                    if (data.length === 0) {
                        suggestionsBox.classList.add("hidden");
                        return;
                    }

                    data.forEach(owner => {
                        let div = document.createElement("div");
                        div.classList.add("px-3", "py-2", "hover:bg-green-100", "cursor-pointer", "text-sm");
                        div.textContent = owner.name;
                        div.addEventListener("click", () => {
                            ownerInput.value = owner.name;
                            document.getElementById("phone").value = owner.phone;
                            document.getElementById("email").value = owner.email;
                            document.getElementById("owner_id").value = owner.id;
                            suggestionsBox.classList.add("hidden");
                        });
                        suggestionsBox.appendChild(div);
                    });

                    suggestionsBox.classList.remove("hidden");
                });
        });

        // Hide suggestions if clicked outside
        document.addEventListener("click", (e) => {
            if (!ownerInput.parentElement.contains(e.target)) {
                suggestionsBox.classList.add("hidden");
            }
        });

        document.getElementById('search').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            rows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const contact = row.cells[1].textContent.toLowerCase();
                row.style.display = (name.includes(searchTerm) || contact.includes(searchTerm)) ? '' : 'none';
            });
        });

    </script>

</body>

</html>