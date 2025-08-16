<?php
    include_once 'admin/session.php';

    if (isLoggedIn()) {
        logout();
    }
?>