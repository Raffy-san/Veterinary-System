<?php
include_once '../config/config.php';
require_once '../functions/session.php';
SessionManager::requireLogin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Pet Management</title>
</head>

<body class="bg-green-100 w-full">
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

                    <div id="ownerSuggestions"
                        class="w-44 absolute top-full left-0 mt-1 w-60 bg-white border border-gray-300 rounded-md shadow-lg hidden z-50 max-h-40 overflow-y-auto">
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
                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700 text-sm">Create
                            Account</button>
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
        </section>
    </main>
    <script>
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
                closeBtn.closest(".modal").classList.add("hidden");
                document.body.style.overflow = "auto"; // Restore background scroll

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

    </script>
    <script>
        const ownerInput = document.getElementById("owner_name");
        const suggestionsBox = document.getElementById("ownerSuggestions");

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
    </script>

</body>

</html>