<?php
// admin/update_theme.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// 检查是否通过POST请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme'])) {
    $theme = $_POST['theme'] === 'dark' ? 'dark' : 'light';
    updateSetting('theme_mode', $theme);
    echo 'success';
} else {
    http_response_code(400);
    echo 'error';
}
?>