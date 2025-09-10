<?php
session_start();

class SessionManager
{
    /**
     * Check if user is logged in.
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Require login, redirect if not logged in.
     */
    public static function requireLogin(string $redirect = '../login.php'): void
    {
        if (!self::isLoggedIn()) {
            self::redirect($redirect);
        }
    }

    /**
     * Require specific role to access a page.
     */
    public static function requireRole(string $accessType, string $redirect = '../unauthorized.php'): void
    {
        if (!isset($_SESSION['access_type']) || $_SESSION['access_type'] !== $accessType) {
            self::redirect($redirect);
        }
    }

    /**
     * Log out the user and redirect.
     */
    public static function logout(string $redirect = 'login.php'): void
    {
        session_unset();
        session_destroy();
        self::redirect($redirect);
    }

    /**
     * Redirect to a given URL and exit.
     */
    private static function redirect(string $url): void
    {
        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
            exit();
        }

        header("Location: $url");
        exit();
    }


    /**
     * Get logged-in user info from database.
     */
    public static function getUser(PDO $pdo): ?array
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        $userId = $_SESSION['user_id'];
        $accessType = $_SESSION['access_type'] ?? null;

        if ($accessType === 'owner') {
            // Fetch from owners + users
            $stmt = $pdo->prepare("
            SELECT o.id AS owner_id, o.name, o.email, o.phone, o.address, o.status, o.created_at,
                   u.id AS user_id, u.email, u.access_type
            FROM owners o
            INNER JOIN users u ON o.user_id = u.id
            WHERE u.id = ?
        ");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } else {
            // Fetch only from users for admin or other roles
            $stmt = $pdo->prepare("
            SELECT id AS user_id, email, password, access_type
            FROM users
            WHERE id = ?
        ");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }
    }


    public static function regenerateCsrfToken()
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }

}
?>