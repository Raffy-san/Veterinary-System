<?php
session_start();
include_once 'config/config.php';

if (isset($_SESSION['user_id'])) {
    // Clear session data
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>