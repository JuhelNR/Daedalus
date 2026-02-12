<?php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['uid']) || empty($_SESSION['uid'])) {
    header('Location: login.php');
    exit();
}

// Optional: Check session timeout (24 hours)
if (isset($_SESSION['login-time'])) {
    $timeout = 86400; // 24 hours in seconds
    if ((time() - $_SESSION['login-time']) > $timeout) {
        session_destroy();
        header('Location: login.php?expired=1');
        exit();
    }
}

?>
