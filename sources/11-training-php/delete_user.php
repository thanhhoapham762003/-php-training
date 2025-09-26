<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header("Content-Type: application/json; charset=UTF-8");

// Chỉ cho phép POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Method not allowed"
    ]);
    exit;
}

// Lấy CSRF token từ POST, KHÔNG lấy từ GET
$token = $_POST['csrf_token'] ?? '';

if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid CSRF token"
    ]);
    exit;
}

// Thực hiện xóa user
$userId = $_POST['id'] ?? null;

if ($userId) {
    // TODO: gọi DB xóa user theo ID
    echo json_encode([
        "status" => "ok",
        "message" => "User deleted successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Missing user ID"
    ]);
}
