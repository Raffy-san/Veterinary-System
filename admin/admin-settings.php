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

if (isset($_POST['update'])) {
    $data = [
        'admin_id' => $_POST['admin_id'],
        'username' => $_POST['username'],
        'password' => $_POST['password']
    ];

    if (
        updateAdmin($pdo, $data)
    ) {
       header("Location:admin-settings.php?updated=1");
       exit();
    } else {
       header("Location:admin-settings.php?updated=0");
       exit();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Admin Settings</title>
</head>

<body class="w-full bg-green-100 min-h-screen overflow-y-auto">
    <?php
    include '../includes/admin-header.php';
    ?>

    <main class="p-10">
        <section class="w-full bg-white shadow-lg rounded-xl p-8">
            <!-- Header -->
            <div class="mb-8 border-b border-gray-200 pb-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cog text-white text-lg"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-800">Admin Settings</h3>
                </div>
                <p class="text-gray-600">Manage your admin settings here.</p>
            </div>

            <form class="space-y-6" method="POST" action="admin-settings.php">
                <input type="text" name="admin_id" value="<?php echo $admin['user_id']; ?>" hidden>
                <!-- Username Field -->
                <div class="space-y-2">
                    <label for="username" class="block text-sm font-medium text-gray-700 flex items-center gap-2">
                        <i class="fas fa-user text-green-500 w-4"></i>
                        Change Username
                    </label>
                    <input type="text" id="username" placeholder="New Username" name="username" required
                        class="border border-gray-300 rounded-lg p-3 mt-1 w-full focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-700 flex items-center gap-2">
                        <i class="fas fa-lock text-green-500 w-4"></i>
                        Change Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" placeholder="New Password" name="password" required
                            class="border border-gray-300 rounded-lg p-3 mt-1 w-full pr-12 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-green-500 transition-colors">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-6">
                    <button type="submit" name="update"
                        class="flex-1 bg-green-500 hover:bg-green-600 text-white py-3 px-6 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                    <button type="button" onclick="resetForm()"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-undo"></i>
                        Reset
                    </button>
                </div>
            </form>
        </section>
    </main>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

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

        function resetForm() {
            document.getElementById('username').value = '';
            document.getElementById('password').value = '';
        }
    </script>
</body>

</html>