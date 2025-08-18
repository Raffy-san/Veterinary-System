<?php
require_once 'functions/session.php';
include_once 'config/config.php';

if (SessionManager::isLoggedIn()) {
    if (isset($_SESSION['access_type']) && $_SESSION['access_type'] === 'admin') {
        header("Location: admin/admin-dashboard.php");
    } else {
        header("Location: owner/owner-dashboard.php");
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/img/green-paw.png">
    <title>Login - Medical Record System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-green-100 flex items-center justify-center min-h-screen">
    <section class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <div class="w-full max-w-md flex justify-center mb-2">
            <i class="fa-solid fa-paw text-6xl text-green-500 rotate-45"></i>
        </div>
        <h2 class="text-2xl font-normal text-center text-gray-800">Veterinary System</h2>
        <h3 class="text-xl font-light mb-4 text-center text-gray-700">Sign in to access your dashboard</h3>
        <form id="loginForm">
            <div class="mb-2">
                <label class="font-bold block text-gray-700 mb-2">Access Type</label>
                <div class="relative inline-block w-full">
                    <div class="bg-white border border-gray-300 rounded-md p-2 cursor-pointer"
                        id="custom-select-trigger" tabindex="0">
                        <i class="fa-solid fa-paw mr-2 text-green-500 rotate-45"></i>
                        <span>Pet Owner</span>
                    </div>
                    <ul class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 hidden"
                        id="custom-select-options">
                        <li class="group p-2 hover:bg-green-500 hover:text-white cursor-pointer flex items-center"
                            data-value="admin">
                            <i class="fa-solid fa-shield mr-2 text-green-500 group-hover:text-white"></i>
                            Admin Access
                        </li>
                        <li class="group p-2 hover:bg-green-500 hover:text-white cursor-pointer flex items-center"
                            data-value="owner">
                            <i class="fa-solid fa-paw mr-2 text-green-500 rotate-45 group-hover:text-white"></i>
                            Pet Owner
                        </li>
                    </ul>
                </div>
                <div id="access-description"
                    class="mt-2 text-gray-600 text-sm border rounded-md px-2 py-4 flex gap-2 flex-col">
                    <div class="flex items-center gap-2">
                        <i id="desc-icon" class="fa-solid fa-paw text-green-500 rotate-45"></i>
                        <span id="desc-text" class="font-semibold">Pet Owner</span>
                    </div>
                    <span id="desc-description">View your pet's medical records</span>
                </div>
            </div>

            <div class="mb-2">
                <label class="font-bold block text-gray-700 mb-2" for="username">Username</label>
                <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500"
                    type="text" id="username" name="username" placeholder="Enter Your Username" required>
            </div>
            <div class="mb-2 relative">
                <label class="font-bold block text-gray-700 mb-2" for="password">Password</label>
                <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500"
                    type="password" id="password" name="password" placeholder="Enter Your Password" required>
                <i id="togglePassword" class="fa-solid fa-eye cursor-pointer absolute right-3 bottom-3 text-gray-600"
                    onclick="togglePasswordVisibility()">
                </i>
            </div>

            <button
                class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-700 transition-colors font-semibold"
                type="submit">
                Sign in
            </button>
        </form>

    </section>
    <script>
        // Descriptions and icons for each option
        const descriptions = {
            admin: {
                icon: '<i class="fa-solid fa-shield text-green-500"></i>',
                text: 'Admin Access',
                description: "Manage clinic operations and create client accounts."
            },
            owner: {
                icon: '<i class="fa-solid fa-paw text-green-500 rotate-45"></i>',
                text: 'Pet Owner',
                description: "View your pet\'s medical records"
            }
        };

        // Set default selected value
        let selectedValue = "owner";
        document.getElementById('custom-select-trigger').dataset.value = selectedValue;

        document.getElementById('custom-select-trigger').addEventListener('click', function () {
            document.getElementById('custom-select-options').classList.toggle('hidden');
        });

        document.querySelectorAll('#custom-select-options li').forEach(item => {
            item.addEventListener('click', function () {
                // Get the icon HTML and the text
                const iconHTML = this.querySelector('i').outerHTML;
                const text = this.textContent.trim();

                // Set the trigger's HTML to icon + text
                document.getElementById('custom-select-trigger').innerHTML = iconHTML + '<span>' + text + '</span>';
                document.getElementById('custom-select-options').classList.add('hidden');
                // Set selected value
                selectedValue = this.dataset.value;
                document.getElementById('custom-select-trigger').dataset.value = selectedValue;
                // Update description icon and text
                document.getElementById('desc-icon').outerHTML = descriptions[selectedValue].icon.replace('">', '" id="desc-icon">');
                document.getElementById('desc-text').textContent = descriptions[selectedValue].text;
                document.getElementById('desc-description').textContent = descriptions[selectedValue].description;
            });
        });

        // On page load, set the default description icon and text
        document.getElementById('desc-icon').outerHTML = descriptions[selectedValue].icon.replace('">', '" id="desc-icon">');
        document.getElementById('desc-text').textContent = descriptions[selectedValue].text;
        document.getElementById('desc-description').textContent = descriptions[selectedValue].description;
    </script>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            fetch('login_handler.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        let path = '';
                        if (data.access_type === 'admin') {
                            path = 'admin/admin-dashboard.php';
                        } else {
                            path = 'owner/owner-dashboard.php';
                        }
                        window.location.href = path;
                    } else {
                        alert(data.message || 'Login failed');
                    }
                })
                .catch(err => {
                    alert('An error occurred. Please try again.');
                    console.error(err);
                });
        });
    </script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>