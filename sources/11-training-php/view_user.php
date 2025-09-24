<?php
require_once 'includes/helpers.php';
require_once 'models/UserModel.php';
$userModel = new UserModel();

$user = NULL;
$id = null;

if (!empty($_GET['id'])) {
    // ensure id is integer
    $id = (int) $_GET['id'];
    if ($id > 0) {
        $user = $userModel->findUserById($id);
    }
}

// If no id provided or user not found, $user may be null
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
    <?php if ($user && !empty($user[0])) { ?>
        <div class="alert alert-warning" role="alert">User profile</div>
        <div>
            <p>Name: <span id="username"><?php echo e($user[0]['name']); ?></span></p>
            <p>Fullname: <span id="fullname"><?php echo e($user[0]['fullname']); ?></span></p>
            <p>Email: <span id="email"><?php echo e($user[0]['email']); ?></span></p>
        </div>
    <?php } else { ?>
        <div class="alert alert-success" role="alert">
            User not found!
        </div>
    <?php } ?>
</div>
</body>
</html>
