<?php
require_once 'models/UserModel.php';
$userModel = new UserModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = $userModel->auth($username, $password);

    if ($user) {
        $userId = $user[0]['id'];
        $username = $user[0]['name'];

        $redis = new Redis();
        $redis->connect('web-redis', 6379);

        $sessionId = bin2hex(random_bytes(16));
        $redis->setex("$sessionId", 3600, json_encode([
            "id" => $userId,
            "username" => $username
        ]));

        echo json_encode([
            "status" => "success",
            "session_id" => $sessionId
        ]);
    } else {
        echo json_encode([
            "status" => "fail",
            "message" => "Login failed"
        ]);
    }
    exit;
}
?>
