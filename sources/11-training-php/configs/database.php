<?php
define('DB_HOST', 'web-mysql');   // service name trong docker-compose
define('DB_USER', 'root');        // hoặc 'user' nếu bạn muốn dùng user thường
define('DB_PASSWORD', 'pass');    // trùng với MYSQL_ROOT_PASSWORD trong docker-compose
define('DB_PORT', 3306);
define('DB_NAME', 'app_web1');    // đúng với tên DB bạn đã import
