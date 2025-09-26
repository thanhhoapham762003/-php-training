<?php
require_once 'helpers/xss.php';
require_once 'models/UserModel.php';
$userModel = new UserModel();

$user = NULL;
$_id = null;

if (!empty($_GET['id'])) {
    $_id = (int) $_GET['id'];
    if ($_id > 0) {
        $user = $userModel->findUserById($_id);
    }
}

if (!empty($_POST['submit'])) {
    // server-side validation: required name and password for insert
    $input = [
        'name' => $_POST['name'] ?? '',
        'fullname' => $_POST['fullname'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
        'type' => $_POST['type'] ?? 'user',
        'id' => $_POST['id'] ?? null
    ];
    if (!empty($_id)) {
        $userModel->updateUser($input);
    } else {
        $userModel->insertUser($input);
    }
    header('Location: list_users.php');
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
    <?php if ($user || !isset($_id)) { ?>
        <div class="alert alert-warning" role="alert">User form</div>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo e($_id); ?>">
            <div class="form-group">
                <label for="name">Name</label>
                <input class="form-control" name="name" placeholder="Name" value="<?php echo e($user[0]['name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="fullname">Fullname</label>
                <input class="form-control" name="fullname" placeholder="Fullname" value="<?php echo e($user[0]['fullname'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input class="form-control" name="email" placeholder="Email" value="<?php echo e($user[0]['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password">
            </div>
            <button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
        </form>
    <?php } else { ?>
        <div class="alert alert-success" role="alert">User not found!</div>
    <?php } ?>
</div>
</body>
</html>
