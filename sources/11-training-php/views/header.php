<?php
// views/header.php
// START: security headers (CSP, HSTS etc)
if (!headers_sent()) {
    // CSP: chỉ cho phép script/style từ chính server và data: cho images (tùy chỉnh theo nhu cầu)
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; object-src 'none'; frame-ancestors 'self'; base-uri 'self';");
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: SAMEORIGIN");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Permissions-Policy: geolocation=(), microphone=()");
}

// Start session if not started (used by your app)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// include helper escape
require_once 'helpers/xss.php';

// Determine current user id if stored in session
$id = '';
if (!empty($_SESSION['id'])) {
    $id = (int) $_SESSION['id'];
}

// keyword for search box (escaped on output)
$keyword = $_GET['keyword'] ?? '';

?>
<div class="container">
    <nav class="navbar navbar-icon-top navbar-default">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="list_users.php">App Web 1</a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="form_user.php">Add new user</a></li>
            </ul>

            <form class="navbar-form navbar-left" method="get" action="list_users.php" role="search">
                <div class="form-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Search users"
                           value="<?php echo e($keyword) ?>">
                </div>
                <button type="submit" class="btn btn-default">Search</button>
            </form>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">
                        <i class="fa fa-user-circle-o"></i>
                        Account <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="view_user.php?id=<?php echo urlencode($id) ?>">Profile</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-warning" role="alert">
            <?php echo e($_SESSION['message']); unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
</div>
