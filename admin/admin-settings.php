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

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/logo.webp">
    <script src="../assets/js/script.js"></script>
    <link rel="stylesheet" href="../assets/css/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Admin Settings</title>
    <style>
        .password-strength-meter {
            height: 5px;
            margin-top: 8px;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .password-weak {
            background-color: #e53e3e;
            width: 33%;
        }

        .password-medium {
            background-color: #dd6b20;
            width: 66%;
        }

        .password-strong {
            background-color: #38a169;
            width: 100%;
        }

        .error-message {
            color: #e53e3e;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .success-message {
            color: #38a169;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        input.error {
            border-color: #e53e3e;
        }

        input.success {
            border-color: #38a169;
        }
    </style>
</head>

<body class="w-full bg-green-100 min-h-screen overflow-y-auto">
    <?php
    include '../includes/admin-header.php';
    ?>

    <main class="p-10 max-w-[1400px] mx-auto">
        <section class="w-full bg-white shadow-lg rounded-xl p-8">
            <!-- Header -->
            <div class="mb-8 border-b border-gray-200 pb-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cog text-white text-lg"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-800">Admin Settings</h3>
                </div>
                <p class="text-gray-600">Manage your admin account settings here.</p>
            </div>

            <form class="space-y-6" method="POST" id="adminSettingsForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="admin_id" value="<?php echo $admin['user_id']; ?>">

                <!-- Current Password Field (for verification) -->
                <div class="space-y-2">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 items-center gap-2">
                        <i class="fas fa-lock text-green-500 w-4"></i>
                        Current Password (for verification)
                    </label>
                    <div class="relative">
                        <input type="password" id="current_password" placeholder="Enter your current password"
                            autocomplete="off" name="current_password"
                            class="border border-gray-300 rounded-lg p-3 mt-1 w-full pr-12 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        <button type="button" onclick="togglePassword('current_password', 'current_toggleIcon')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-green-500 transition-colors">
                            <i class="fas fa-eye mt-2 cursor-pointer" id="current_toggleIcon"></i>
                        </button>
                    </div>
                    <div id="current_password_error" class="error-message"></div>
                </div>

                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-700 items-center gap-2">
                        <i class="fas fa-envelope text-green-500 w-4"></i>
                        Change Email
                    </label>
                    <input type="email" id="email" placeholder="New Email" name="email"
                        value="<?php echo htmlspecialchars($admin['email']); ?>"
                        class="border border-gray-300 rounded-lg p-3 mt-1 w-full focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    <div id="email_error" class="error-message"></div>
                </div>

                <!-- New Password Field -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-700 items-center gap-2">
                        <i class="fas fa-lock text-green-500 w-4"></i>
                        New Password (leave blank to keep current)
                    </label>
                    <div class="relative">
                        <input type="password" id="password" placeholder="New Password" name="password"
                            autocomplete="off"
                            class="border border-gray-300 rounded-lg p-3 mt-1 w-full pr-12 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        <button type="button" onclick="togglePassword('password', 'toggleIcon')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-green-500 transition-colors">
                            <i class="fas fa-eye mt-2 cursor-pointer" id="toggleIcon"></i>
                        </button>
                    </div>
                    <div id="password_strength" class="password-strength-meter"></div>
                    <div id="password_error" class="error-message"></div>
                    <p class="text-xs text-gray-500 mt-1">Password must be at least 8 characters and include uppercase,
                        lowercase, number, and special character.</p>
                </div>

                <!-- Confirm Password Field -->
                <div class="space-y-2" id="confirm_password_container" style="display: none;">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 items-center gap-2">
                        <i class="fas fa-lock text-green-500 w-4"></i>
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <input type="password" id="confirm_password" placeholder="Confirm New Password"
                            autocomplete="off" name="confirm_password"
                            class="border border-gray-300 rounded-lg p-3 mt-1 w-full pr-12 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        <button type="button" onclick="togglePassword('confirm_password', 'confirm_toggleIcon')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-green-500 transition-colors">
                            <i class="fas fa-eye mt-2 cursor-pointer" id="confirm_toggleIcon"></i>
                        </button>
                    </div>
                    <div id="confirm_password_error" class="error-message"></div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-6">
                    <button type="submit" name="update" id="submitButton"
                        class="flex-1 cursor-pointer bg-green-500 hover:bg-green-600 text-white py-3 px-6 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                    <button type="button" onclick="resetToOriginalValues()"
                        class="px-6 cursor-pointer py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-undo"></i>
                        Reset
                    </button>
                </div>
            </form>
            <?php include '../includes/message-modal.php' ?>

        </section>
    </main>

    <script>
        // Store original values for reset functionality
        const originalEmail = "<?php echo htmlspecialchars($admin['email']); ?>";
        let originalPassword = "";

        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        function resetToOriginalValues() {
            document.getElementById('email').value = originalEmail;
            document.getElementById('password').value = '';
            document.getElementById('confirm_password').value = '';
            document.getElementById('current_password').value = '';

            // Clear error messages
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('input').forEach(input => {
                input.classList.remove('error');
                input.classList.remove('success');
            });

            // Hide confirm password if not needed
            document.getElementById('confirm_password_container').style.display = 'none';
            document.getElementById('password_strength').className = 'password-strength-meter';
        }

        function validatePassword(password) {
            // At least 8 characters, one uppercase, one lowercase, one number, one special character
            const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            const mediumRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/;

            if (password.length === 0) {
                return { strength: 'none', message: '' };
            } else if (strongRegex.test(password)) {
                return { strength: 'strong', message: 'Strong password' };
            } else if (mediumRegex.test(password)) {
                return { strength: 'medium', message: 'Medium strength password. Add a special character for better security.' };
            } else if (password.length >= 8) {
                return { strength: 'weak', message: 'Weak password. Include uppercase, lowercase, and numbers.' };
            } else {
                return { strength: 'weak', message: 'Password must be at least 8 characters' };
            }
        }

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const errorElement = document.getElementById('confirm_password_error');

            if (password && confirmPassword && password !== confirmPassword) {
                errorElement.textContent = 'Passwords do not match';
                document.getElementById('confirm_password').classList.add('error');
                document.getElementById('confirm_password').classList.remove('success');
                return false;
            } else if (password && confirmPassword && password === confirmPassword) {
                errorElement.textContent = '';
                document.getElementById('confirm_password').classList.remove('error');
                document.getElementById('confirm_password').classList.add('success');
                return true;
            }

            errorElement.textContent = '';
            return true;
        }

        function validateForm() {
            let isValid = true;

            // Validate current password
            const currentPassword = document.getElementById('current_password').value;
            if (!currentPassword) {
                document.getElementById('current_password_error').textContent = 'Current password is required';
                document.getElementById('current_password').classList.add('error');
                isValid = false;
            } else {
                document.getElementById('current_password_error').textContent = '';
                document.getElementById('current_password').classList.remove('error');
            }

            // Validate email
            const email = document.getElementById('email').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email) {
                document.getElementById('email_error').textContent = 'Email is required';
                document.getElementById('email').classList.add('error');
                isValid = false;
            } else if (!emailRegex.test(email)) {
                document.getElementById('email_error').textContent = 'Please enter a valid email address';
                document.getElementById('email').classList.add('error');
                isValid = false;
            } else {
                document.getElementById('email_error').textContent = '';
                document.getElementById('email').classList.remove('error');
            }

            // Validate password if provided
            const password = document.getElementById('password').value;
            if (password) {
                const passwordValidation = validatePassword(password);

                if (passwordValidation.strength === 'weak') {
                    document.getElementById('password_error').textContent = passwordValidation.message;
                    document.getElementById('password').classList.add('error');
                    isValid = false;
                } else {
                    document.getElementById('password_error').textContent = '';
                    document.getElementById('password').classList.remove('error');
                }

                // Check password match
                if (!checkPasswordMatch()) {
                    isValid = false;
                }
            }

            return isValid;
        }

        function showMessage(title, message, type = "success") {
            const modal = document.getElementById("messageModal");
            const titleElement = document.getElementById("messageTitle");
            const textElement = document.getElementById("messageText");

            titleElement.textContent = title;
            textElement.textContent = message;

            if (type === "success") {
                titleElement.classList.remove("text-red-600");
                titleElement.classList.add("text-green-600");
            } else {
                titleElement.classList.remove("text-green-600");
                titleElement.classList.add("text-red-600");
            }

            modal.classList.remove("hidden");
            modal.classList.add("flex");
            updateBodyScroll();

            document.getElementById("closeMessageBtn").onclick = () => {
                modal.classList.add("hidden");
                updateBodyScroll();
                if (type === "success") {
                    location.reload(); // Reload after success
                }
            };
        }

        // Event listeners
        document.getElementById('password').addEventListener('input', function () {
            const password = this.value;
            const strengthMeter = document.getElementById('password_strength');
            const confirmContainer = document.getElementById('confirm_password_container');

            if (password) {
                confirmContainer.style.display = 'block';
                const validation = validatePassword(password);

                strengthMeter.className = 'password-strength-meter';
                if (validation.strength !== 'none') {
                    strengthMeter.classList.add(`password-${validation.strength}`);
                }

                document.getElementById('password_error').textContent = validation.message;
                if (validation.strength === 'weak') {
                    this.classList.add('error');
                    this.classList.remove('success');
                } else {
                    this.classList.remove('error');
                    this.classList.add('success');
                }
            } else {
                confirmContainer.style.display = 'none';
                strengthMeter.className = 'password-strength-meter';
                document.getElementById('password_error').textContent = '';
                this.classList.remove('error');
                this.classList.remove('success');
            }

            // Check password match if confirm field has value
            if (document.getElementById('confirm_password').value) {
                checkPasswordMatch();
            }
        });

        document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

        // Handle Add Client Form (AJAX)
        document.getElementById("adminSettingsForm").addEventListener("submit", function (e) {
            e.preventDefault();

            if (!validateForm()) {
                showMessage("Validation Error", "Please fix the errors in the form.", "error");
                return;
            }

            const formData = new FormData(this);
            const submitButton = document.getElementById('submitButton');

            // Disable button to prevent multiple submissions
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            fetch("../php/Update/update-admin.php", {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        showMessage("Success", data.message, "success");
                    } else {
                        showMessage("Error", data.message, "error");
                    }
                })
                .catch(error => {
                    showMessage("Error", "Something went wrong!", "error");
                    console.error("Fetch Error:", error);
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save"></i> Save Changes';
                });
        });

    </script>
</body>

</html>