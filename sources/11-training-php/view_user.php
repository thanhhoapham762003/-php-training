<?php
require_once 'models/UserModel.php';
$userModel = new UserModel();

// Kết nối Redis
$redis = new Redis();
$redis->connect('web-redis', 6379);

// Nếu request là Ajax (JS fetch), trả JSON
if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    $sessionUser = null;

    if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        $sessionId = $matches[1];
        $userJson = $redis->get("session:$sessionId");
        if ($userJson) {
            $sessionUser = json_decode($userJson, true);
        }
    }

    if (!$sessionUser) {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['status' => 'fail', 'message' => 'Not logged in']);
        exit;
    }

    // ID user để view (mặc định là user login, có thể override bằng ?id)
    $userIdToView = $sessionUser['id'];
    if (!empty($_GET['id'])) {
        $userIdToView = (int)$_GET['id'];
    }

    $user = $userModel->findUserById($userIdToView);

    if ($user) {
        echo json_encode(['status' => 'success', 'user' => $user[0]]);
    } else {
        echo json_encode(['status' => 'fail', 'message' => 'User not found']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <?php include 'views/meta.php'; ?>
</head>
<body>
<?php include 'views/header.php'; ?>
<div class="container">
    <div class="alert alert-warning">User profile</div>
    <div>
        <p>Name: <span id="username"></span></p>
        <p>Fullname: <span id="fullname"></span></p>
        <p>Email: <span id="email"></span></p>
    </div>
</div>
<script type="text/javascript" src="public/js/login.js"></script>

</body>
</html>
