<?php
// includes/config.php

// 数据库配置
define('DB_HOST', 'localhost');
define('DB_NAME', '数据库名');
define('DB_USER', '数据库名');
define('DB_PASS', '密码');
define('DB_CHARSET', 'utf8mb4');

// 网站配置
define('SITE_URL', 'http://localhost/xingcms');
define('SITE_NAME', '星系统 XingCMS');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// 开启会话
session_start();

// 数据库连接
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER, 
        DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

// 自动加载函数
function autoloadClasses($className) {
    $file = __DIR__ . '/../classes/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('autoloadClasses');
?>