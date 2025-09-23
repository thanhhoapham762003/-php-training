<?php
// Kết nối Redis
$redis = new Redis();
$redis->connect('web-redis', 6379);

// Lấy sessionId từ cookie nếu có
$sessionId = $_COOKIE['sessionId'] ?? null;

if ($sessionId) {
    // Xóa session trong Redis
    $redis->del("session:$sessionId");

    // Xóa cookie trên client
    setcookie('sessionId', '', time() - 3600, '/');
}

// Trả HTML + JS xóa LocalStorage và redirect về login
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Logout</title>
</head>
<body>
<script>
    // Xóa sessionId trong LocalStorage
    localStorage.removeItem('sessionId');
    // Chuyển hướng về login
    window.location.href = 'login.php';
</script>
</body>
</html>
