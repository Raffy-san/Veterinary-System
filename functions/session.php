<?php
session_start();

class SessionManager
{
    /**
     * Check if user is logged in.
     * @return bool
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Require login, redirect if not logged in.
     * @param string $redirect
     */
    public static function requireLogin(string $redirect = '../login.php'): void
    {
        if (!self::isLoggedIn()) {
            self::redirect($redirect);
        }
    }

    /**
     * Log out the user and redirect.
     * @param string $redirect
     */
    public static function logout(string $redirect = 'login.php'): void
    {
        session_unset();
        session_destroy();
        self::redirect($redirect);
    }

    /**
     * Redirect to a given URL and exit.
     * @param string $url
     */
    private static function redirect(string $url): void
    {
        header("Location: $url");
        exit();
    }

    /**
     * Get logged-in user info from database.
     * @param PDO $pdo
     * @return array|null
     */
    public static function getUser(PDO $pdo): ?array
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        $stmt = $pdo->prepare("
        SELECT o.id AS owner_id, o.name, o.email, o.phone, o.address, o.status, o.created_at, u.id AS user_id, u.username
        FROM owners o
        INNER JOIN users u ON o.user_id = u.id
        WHERE u.id = ?
    ");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
?>