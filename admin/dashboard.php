<?php
// admin/dashboard.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// 检查登录状态
if (!isLoggedIn()) {
    redirect('login.php');
}

// 获取统计数据
$articleCount = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$publishedArticleCount = $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
$categoryCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$pageCount = $pdo->query("SELECT COUNT(*) FROM pages")->fetchColumn();

// 获取最近文章
$recentArticles = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC LIMIT 5")->fetchAll();

$siteName = getSetting('site_name', '星系统 XingCMS');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>控制台 - 后台管理</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            display: flex;
        }
        
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            height: 100vh;
            position: fixed;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar-header h2 {
            color: #3498db;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
        }
        
        .sidebar-menu li {
            padding: 0;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover, 
        .sidebar-menu a.active {
            background: #34495e;
            color: white;
        }
        
        .sidebar-menu a i {
            margin-right: 10px;
        }
        
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .recent-articles {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .recent-articles h2 {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .article-list {
            list-style: none;
        }
        
        .article-item {
            padding: 10px 0;
            border-bottom: 1px solid #ecf0f1;
            display: flex;
            justify-content: space-between;
        }
        
        .article-item:last-child {
            border-bottom: none;
        }
        
        .article-title {
            font-weight: 500;
        }
        
        .article-date {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info span {
            margin-right: 10px;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- 侧边栏 -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>星系统 CMS</h2>
            <p>后台管理</p>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active">控制台</a></li>
            <li><a href="articles.php">文章管理</a></li>
            <li><a href="categories.php">分类管理</a></li>
            <li><a href="pages.php">单页管理</a></li>
            <li><a href="settings.php">系统设置</a></li>
            <li><a href="logout.php">退出登录</a></li>
        </ul>
    </div>
    
    <!-- 主内容区 -->
    <div class="main-content">
        <div class="header">
            <h1>控制台</h1>
            <div class="user-info">
                <span>欢迎，<?php echo $_SESSION['username']; ?></span>
                <a href="logout.php" class="logout-btn">退出</a>
            </div>
        </div>
        
        <!-- 统计卡片 -->
        <div class="stats">
            <div class="stat-card">
                <h3>总文章数</h3>
                <div class="number"><?php echo $articleCount; ?></div>
            </div>
            <div class="stat-card">
                <h3>已发布文章</h3>
                <div class="number"><?php echo $publishedArticleCount; ?></div>
            </div>
            <div class="stat-card">
                <h3>分类数量</h3>
                <div class="number"><?php echo $categoryCount; ?></div>
            </div>
            <div class="stat-card">
                <h3>单页数量</h3>
                <div class="number"><?php echo $pageCount; ?></div>
            </div>
        </div>
        
        <!-- 最近文章 -->
        <div class="recent-articles">
            <h2>最近文章</h2>
            <ul class="article-list">
                <?php foreach ($recentArticles as $article): ?>
                    <li class="article-item">
                        <span class="article-title"><?php echo $article['title']; ?></span>
                        <span class="article-date"><?php echo date('Y-m-d', strtotime($article['created_at'])); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>