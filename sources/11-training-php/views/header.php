<?php
// views/header.php - version hardened to show profile of logged-in user

// include helper escape
require_once 'helpers/xss.php';

// ensure session is started for $_SESSION usage
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If a page already provided $sessionUser, use it; otherwise try to read from cookie + Redis
$sessionUser = $sessionUser ?? null;

if (empty($sessionUser)) {
    $sessionId = $_COOKIE['sessionId'] ?? '';
    if ($sessionId && extension_loaded('redis')) {
        try {
            $redis = new Redis();
            // change host/port if your redis config differs
            $redis->connect('web-redis', 6379);
            $userJson = $redis->get("$sessionId");
            if ($userJson) {
                $decoded = json_decode($userJson, true);
                if (is_array($decoded)) {
                    $sessionUser = $decoded;
                }
            }
        } catch (Throwable $e) {
            // ignore Redis errors here to avoid breaking the page
            $sessionUser = $sessionUser ?? null;
        }
    }
}

// safe values for display
$currentUserId = isset($sessionUser['id']) ? (string)$sessionUser['id'] : '';
$currentUsername = isset($sessionUser['username']) ? (string)$sessionUser['username'] : '';

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
                        <?php if ($currentUsername !== ''): ?>
                            <?php echo e($currentUsername); ?>
                        <?php else: ?>
                            Account
                        <?php endif; ?>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if ($currentUserId !== ''): ?>
                            <li><a href="view_user.php?id=<?php echo urlencode($currentUserId); ?>">Profile</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Login</a></li>
                        <?php endif; ?>
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
