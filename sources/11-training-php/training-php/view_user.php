<?php
$redis = new Redis();
$redis->connect('web-redis', 6379);

// Lấy sessionId từ cookie
$sessionId = $_COOKIE['sessionId'] ?? '';
$sessionUser = null;

if ($sessionId) {
    $userJson = $redis->get("session:$sessionId");
    if ($userJson) {
        $sessionUser = json_decode($userJson, true);
    }
}

if (!$sessionUser) {
    header('Location: login.php');
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

    <?php if ($user || empty($id)) { ?>
        <div class="alert alert-warning" role="alert">
            User profile
        </div>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <div class="form-group">
                <label for="name">Name</label>
                <span><?php if (!empty($user[0]['name'])) echo $user[0]['name'] ?></span>
            </div>
            <div class="form-group">
                <label for="password">Fullname</label>
                <span><?php if (!empty($user[0]['name'])) echo $user[0]['fullname'] ?></span>
            </div>
            <div class="form-group">
                <label for="password">Email</label>
                <span><?php if (!empty($user[0]['name'])) echo $user[0]['email'] ?></span>
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