<?php
session_start();

/**
 * Class SessionManager
 * Handles user session logic.
 */

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
}
?>