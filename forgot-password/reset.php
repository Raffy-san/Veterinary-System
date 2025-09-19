<?php
require_once "../config/config.php";
require_once '../functions/session.php';
date_default_timezone_set('Asia/Manila');

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

if (!isset($_GET['token'])) {
    die("Invalid or expired reset link.");
}

$token = $_GET['token'];

// Look up the token in DB
$stmt = $pdo->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("Invalid or expired reset link. (No user found)");
}

// Check expiration
if (strtotime($user['reset_expires']) < time()) {
    die("Invalid or expired reset link. (Token expired)");
}

$message = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // CSRF check
    if (
        !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $message = "Invalid CSRF token.";
    } else {
        $password = $_POST['password'] ?? '';
        // Server-side password strength check
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $message = "Password must be at least 8 characters, include a number and an uppercase letter.";
        } else {
            $newPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users 
                                   SET password = ?, reset_token = NULL, reset_expires = NULL 
                                   WHERE id = ?");
            $stmt->execute([$newPassword, $user['id']]);
            $message = "Password has been reset successfully!";
            // Optionally unset CSRF token after use
            unset($_SESSION['csrf_token']);
            // Redirect after 3 seconds
            echo "<script>
                setTimeout(function() {
                    window.location.href = '../login.php';
                }, 3000);
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/green-paw.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Reset Password</title>
</head>

<body class="bg-green-100 flex items-center justify-center min-h-screen px-4">
    <section class="w-full max-w-md bg-white rounded-lg shadow-md p-6 sm:p-8">
        <div>
            <h2 class="text-2xl sm:text-2xl font-bold text-center mb-4">Set New Password</h2>
            <form method="POST" id="resetForm" autocomplete="off">
                <div class="relative mb-4">
                    <input
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500 text-sm sm:text-base"
                        type="password" name="password" id="password" placeholder="Enter new password" required>
                    <button type="button" id="togglePassword" class="absolute right-3 top-2 text-gray-500">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div id="password_error" class="error-message mb-2"></div>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <button
                    class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-700 transition-colors font-semibold text-sm sm:text-base"
                    type="submit">Reset Password</button>
            </form>
        </div>
        <?php include '../includes/message-modal.php' ?>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script>
        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Client-side password strength validation
        document.getElementById('password').addEventListener('input', function () {
            const val = this.value;
            let msg = '';
            if (val.length < 8) {
                msg = 'Password must be at least 8 characters.';
            } else if (!/[A-Z]/.test(val)) {
                msg = 'Password must include an uppercase letter.';
            } else if (!/[0-9]/.test(val)) {
                msg = 'Password must include a number.';
            }
            document.getElementById('password_error').textContent = msg;
        });

        document.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById("messageModal");
            const closeBtn = document.getElementById("closeMessageBtn");

            closeBtn.addEventListener("click", () => {
                modal.classList.add("hidden");
            });

            <?php if (!empty($message)): ?>
                document.getElementById('messageTitle').textContent = 'Password Reset';
                document.getElementById('messageText').textContent = '<?= $message ?>';
                modal.classList.remove("hidden");
                modal.classList.add("flex");
            <?php endif; ?>
        });
    </script>
</body>

</html>