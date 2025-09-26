<?php
require_once 'helpers/xss.php';
require_once 'models/UserModel.php';

// start session because header.php may expect it (and for csrf token etc.)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// connect redis
$redis = new Redis();
$redis->connect('web-redis', 6379);

// Lấy sessionId từ cookie
$sessionId = $_COOKIE['sessionId'] ?? '';
$sessionUser = null;

if ($sessionId) {
    $userJson = $redis->get("$sessionId");
    if ($userJson) {
        $sessionUser = json_decode($userJson, true);
    }
}

if (!$sessionUser) {
    header('Location: login.php');
    exit;
}

// load user by GET id (same logic as form_user.php)
$userModel = new UserModel();
$id = $_GET['id'] ?? null;
$user = null;
if (!empty($id) && ctype_digit((string)$id)) {
    $user = $userModel->findUserById((int)$id);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User profile</title>
    <?php include 'views/meta.php' ?>
</head>
<body>
<?php
// Make $sessionUser available to header.php
// header.php will use $sessionUser to render profile link, username, etc.
include 'views/header.php';
?>
<div class="container">
    <?php if (!empty($user)) { ?>
        <div class="alert alert-warning" role="alert">
            User profile
        </div>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo e($id); ?>">
            <div class="form-group">
                <label for="name">Name</label>
                <span><?php if (!empty($user[0]['name'])) echo e($user[0]['name']); ?></span>
            </div>
            <div class="form-group">
                <label for="fullname">Fullname</label>
                <span><?php if (!empty($user[0]['fullname'])) echo e($user[0]['fullname']); ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <span><?php if (!empty($user[0]['email'])) echo e($user[0]['email']); ?></span>
            </div>
        </form>
    <?php } else { ?>
        <div class="alert alert-success" role="alert">
            User not found!
        </div>
    <?php } ?>
</div>
</body>
</html>
