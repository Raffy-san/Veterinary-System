<?php
include_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/functions/session.php';

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
    <link rel="stylesheet" href="assets/css/output.css">
    <script src="assets/js/script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body
    class="flex items-center justify-center min-h-screen px-4 bg-[url('../img/bg.png')] bg-scroll sm:bg-fixed bg-no-repeat bg-center bg-cover">
    <section class="w-full max-w-md bg-white rounded-lg shadow-xl p-6 sm:p-8">
        <!-- Icon -->
        <div class="w-full flex justify-center mb-2">
            <i class="fa-solid fa-paw text-5xl sm:text-6xl text-green-500 rotate-45"></i>
        </div>

        <!-- Headings -->
        <h2 class="text-xl sm:text-2xl font-normal text-center text-gray-800">
            Veterinary System
        </h2>
        <h3 class="text-lg sm:text-xl font-light mb-4 text-center text-gray-700">
            Sign in to access your dashboard
        </h3>
        <div class="mb-4">
            <a href="homepage.php"
                class="inline-flex items-center gap-2 text-green-600 font-semibold hover:text-green-800 transition text-sm sm:text-base">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Homepage
            </a>
        </div>

        <!-- Login Form -->
        <form id="loginForm">
            <!-- Access Type -->
            <div class="mb-3">
                <label class="font-bold block text-gray-700 mb-2 text-sm sm:text-base">Access Type</label>
                <div class="relative w-full">
                    <div class="bg-white border border-gray-300 rounded-md p-2 text-sm sm:text-base cursor-pointer flex items-center"
                        id="custom-select-trigger" tabindex="0">
                        <i class="fa-solid fa-paw mr-2 text-green-500 rotate-45"></i>
                        <span>Owner Login</span>
                    </div>
                    <ul class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 hidden text-sm sm:text-base"
                        id="custom-select-options">
                        <li class="group p-2 hover:bg-green-500 hover:text-white cursor-pointer flex items-center"
                            data-value="admin">
                            <i class="fa-solid fa-shield mr-2 text-green-500 group-hover:text-white"></i>
                            Admin Login
                        </li>
                        <li class="group p-2 hover:bg-green-500 hover:text-white cursor-pointer flex items-center"
                            data-value="owner">
                            <i class="fa-solid fa-paw mr-2 text-green-500 rotate-45 group-hover:text-white"></i>
                            Owner Login
                        </li>
                    </ul>
                </div>

                <!-- Description -->
                <div id="access-description"
                    class="mt-2 text-gray-600 text-xs sm:text-sm border border-gray-300 rounded-md px-2 py-3 flex gap-2 flex-col">
                    <div class="flex items-center gap-2">
                        <i id="desc-icon" class="fa-solid fa-paw text-green-500 rotate-45"></i>
                        <span id="desc-text" class="font-semibold">Pet Owner</span>
                    </div>
                    <span id="desc-description">View your pet's medical records</span>
                </div>
            </div>

            <!-- email -->
            <div class="mb-3">
                <label class="font-bold block text-gray-700 mb-2 text-sm sm:text-base" for="email">Email</label>
                <input
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 text-sm sm:text-base"
                    type="email" id="email" name="email" placeholder="Enter Your email" required>
            </div>

            <!-- Password -->
            <div class="relative">
                <label class="font-bold block text-gray-700 mb-2 text-sm sm:text-base" for="password">Password</label>
                <input
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 text-sm sm:text-base"
                    type="password" id="password" name="password" placeholder="Enter Your Password" required>
                <i id="togglePassword"
                    class="fa-solid fa-eye cursor-pointer absolute right-3 bottom-2 py-1 text-gray-600 text-sm sm:text-base">
                </i>
            </div>
            <!-- Button -->
            <button
                class="w-full mt-4 cursor-pointer bg-green-500 text-white py-2 rounded hover:bg-green-700 transition-colors font-semibold text-sm sm:text-base"
                type="submit">
                Sign in
            </button>

        </form>

        <?php include 'includes/message-modal.php' ?>

    </section>
    <script>
        // Descriptions and icons for each option
        const descriptions = {
            admin: {
                icon: '<i class="fa-solid fa-shield text-green-500"></i>',
                text: 'Admin Login',
                description: "Manage clinic operations and create client accounts."
            },
            owner: {
                icon: '<i class="fa-solid fa-paw text-green-500 rotate-45"></i>',
                text: 'Owner Login',
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
                        document.getElementById('messageTitle').textContent = "Login Failed";
                        document.getElementById('messageText').textContent = data.message || 'Login failed';
                        document.getElementById('messageModal').classList.remove('hidden');
                        document.getElementById('messageModal').classList.add('flex');
                        updateBodyScroll();
                    }
                })
                .catch(err => {
                    document.getElementById('messageTitle').textContent = "Error";
                    document.getElementById('messageText').textContent = 'An error occurred. Please try again.';
                    document.getElementById('messageModal').classList.remove('hidden');
                    document.getElementById('messageModal').classList.add('flex');
                    updateBodyScroll();
                    console.error(err);
                });

        });

        document.getElementById('closeMessageBtn').addEventListener('click', function () {
            document.getElementById('messageModal').classList.add('hidden');

            updateBodyScroll();
        });

        document.getElementById('messageModal').addEventListener('click', function (e) {
            if (e.target === this) {
                this.classList.add('hidden');
                updateBodyScroll();
            }
        });


        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>