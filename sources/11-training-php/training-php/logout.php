<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xoá riêng CSRF token
unset($_SESSION['csrf_token']);

// Xoá hết session (khuyến khích)
$_SESSION = [];
session_destroy();
header('location: login.php');
?>