<?php
require_once "../config/config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

// Detect environment
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    $baseURL = "http://localhost/Veterinary-System";
} else {
    $baseURL = "https://yourdomain.com";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(16));
        date_default_timezone_set("Asia/Manila");
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $stmt->execute([$token, $expires, $email]);

        // Build reset link dynamically
        $resetLink = $baseURL . "/forgot-password/reset.php?token=$token";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'veterinarysystem3@gmail.com';
            $mail->Password = 'ounahrsduaewxbnj';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('veterinarysystem3@gmail.com', 'Veterinary System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
                <p>We received a password reset request.</p>
                <p><a href='$resetLink'>Click here to reset your password</a></p>
                <p>This link will expire in 1 hour.</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo}";
        }
    }

    $message = "If that email exists, a reset link has been sent.";

    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('messageTitle').textContent = 'Password Reset';
            document.getElementById('messageText').textContent = '$message';
            document.getElementById('messageModal').classList.remove('hidden');
        });
      </script>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/green-paw.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Forgot Password</title>
</head>

<body class="bg-green-100 flex items-center justify-center min-h-screen px-4">
    <section class="w-full max-w-md bg-white rounded-lg shadow-md p-6 sm:p-8">
        <div>
            <h2 class="text-2xl sm:text-2xl font-bold text-center mb-4">Forgot Password</h2>
            <form action="" method="POST">
                <input
                    class="w-full px-3 py-2 mb-4 border rounded focus:outline-none focus:ring-2 focus:ring-green-500 text-sm sm:text-base"
                    type="email" name="email" placeholder="Enter your email" required>
                <div class="flex flex-1 w-full gap-4">
                    <button type="submit"
                        class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-700 transition-colors font-semibold text-sm sm:text-base">Send
                        Reset Link</button>
                    <button onclick="window.history.back()"
                        class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-700 transition-colors font-semibold text-sm sm:text-base">Back
                        To Login</button>
                </div>
            </form>
        </div>
        <?php include '../includes/message-modal.php' ?>
    </section>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById("messageModal");
            const closeBtn = document.getElementById("closeMessageBtn");

            closeBtn.addEventListener("click", () => {
                modal.classList.add("hidden");
            });
        });
    </script>

</body>

</html>