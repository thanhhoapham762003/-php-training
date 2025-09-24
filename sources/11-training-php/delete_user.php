<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'models/UserModel.php';
$userModel = new UserModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        die("Invalid CSRF token");
    }

    $id = $_POST['id'] ?? null;
    if (!empty($id)) {
        $userModel->deleteUserById($id);
    }
}
header('location: list_users.php');
exit;
