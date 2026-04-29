<?php
// includes/functions.php

// 获取设置值
function getSetting($key, $default = '') {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : $default;
}

// 更新设置值
function updateSetting($key, $value) {
    global $pdo;
    $stmt = $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    return $stmt->execute([$key, $value]);
}

// 获取分类
function getCategories($parent_id = 0) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? AND status = 'active' ORDER BY sort_order, name");
    $stmt->execute([$parent_id]);
    return $stmt->fetchAll();
}

// 获取所有分类（用于下拉菜单）
function getAllCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name");
    return $stmt->fetchAll();
}

// 获取文章
function getArticles($limit = 8, $offset = 0) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT a.*, c.name as category_name 
        FROM articles a 
        LEFT JOIN categories c ON a.category_id = c.id 
        WHERE a.status = 'published' 
        ORDER BY a.created_at DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// 获取单页
function getPages() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM pages WHERE status = 'published' ORDER BY title");
    return $stmt->fetchAll();
}

// 安全过滤输入
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// 生成slug
function generateSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    
    if (empty($text)) {
        return 'n-a';
    }
    
    return $text;
}

// 重定向
function redirect($url) {
    header("Location: $url");
    exit;
}

// 检查用户是否登录
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// 获取当前用户信息
function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

// 检查用户权限
function hasPermission($requiredRole) {
    $user = getCurrentUser();
    if (!$user) return false;
    
    $roles = ['author' => 1, 'editor' => 2, 'admin' => 3];
    return $roles[$user['role']] >= $roles[$requiredRole];
}
?>