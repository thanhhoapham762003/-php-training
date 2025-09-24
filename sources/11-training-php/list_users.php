<?php
require_once 'helpers/xss.php';
require_once 'models/UserModel.php';
$userModel = new UserModel();

$params = [];
if (!empty($_GET['keyword'])) {
    // trim and use raw; SQL injection mitigation should be handled in model (prepared statements) — here we just pass param
    $params['keyword'] = trim($_GET['keyword']);
}

$users = $userModel->getUsers($params);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <?php include 'views/meta.php' // keep existing meta include ?>
</head>
<body>
<?php include 'views/header.php' ?>
<div class="container">
    <?php if (!empty($users)) { ?>
        <div class="alert alert-warning" role="alert">
            List of users!
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Fullname</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u) { ?>
                <tr>
                    <td><?php echo e($u['id']); ?></td>
                    <td><?php echo e($u['name']); ?></td>
                    <td><?php echo e($u['fullname']); ?></td>
                    <td><?php echo e($u['type']); ?></td>
                    <td>
                        <a href="form_user.php?id=<?php echo urlencode($u['id']); ?>"><i class="fa fa-pencil-square-o" title="Update"></i></a>
                        <a href="view_user.php?id=<?php echo urlencode($u['id']); ?>"><i class="fa fa-eye" title="View"></i></a>
                        <a href="delete_user.php?id=<?php echo urlencode($u['id']); ?>" onclick="return confirm('Are you sure?')"><i class="fa fa-eraser" title="Delete"></i></a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-dark" role="alert">
            No users found!
        </div>
    <?php } ?>
</div>
</body>
</html>
