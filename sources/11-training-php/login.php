<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'models/UserModel.php';
$userModel = new UserModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = $userModel->auth($username, $password);

    if ($user) {
        $userId = $user[0]['id'];
        $username = $user[0]['name'];

        // Kết nối Redis
        $redis = new Redis();
        $redis->connect('web-redis', 6379);

        // Tạo session riêng cho user
        $sessionId = bin2hex(random_bytes(16));
        $redis->setex("$sessionId", 3600, json_encode([
            "id" => $userId,
            "username" => $username
        ]));

        // Sinh CSRF token và lưu vào session
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        setcookie('sessionId', $sessionId, time() + 3600, '/', '', $secure, true);

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

<!DOCTYPE html>
<html>
<head>
    <title>User form</title>
    <?php include 'views/meta.php' ?>
</head>
<body>
<?php include 'views/header.php'?>

<div class="container">
    <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info" >
            <div class="panel-heading">
                <div class="panel-title">Login</div>
            </div>

            <div style="padding-top:30px" class="panel-body" >
                <form id="loginForm">
                    <div class="margin-bottom-25 input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="login-username" type="text" class="form-control" name="username" placeholder="username or email">
                    </div>

                    <div class="margin-bottom-25 input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
                    </div>

                    <div class="margin-bottom-25 input-group">
                        <div class="col-sm-12 controls">
                            <button type="button" id="btnLogin" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('btnLogin').addEventListener('click', function() {
        const form = document.getElementById('loginForm');
        const formData = new FormData(form);

        fetch('login.php', { method: 'POST', body: formData , credentials: 'include' })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                localStorage.setItem('sessionId', data.session_id);
                window.location.href = 'list_users.php';
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error(err));
    });
});
</script>
</body>
</html>
