<?php
// category.php - 分类页面
require_once 'includes/config.php';
require_once 'includes/functions.php';

// 获取分类ID
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($category_id <= 0) {
    header('HTTP/1.0 404 Not Found');
    die('分类不存在');
}

// 获取分类信息
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ? AND status = 'active'");
$stmt->execute([$category_id]);
$category = $stmt->fetch();

if (!$category) {
    header('HTTP/1.0 404 Not Found');
    die('分类不存在');
}

// 获取该分类下的文章
$stmt = $pdo->prepare("
    SELECT * FROM articles 
    WHERE category_id = ? AND status = 'published' 
    ORDER BY created_at DESC
");
$stmt->execute([$category_id]);
$articles = $stmt->fetchAll();

// 获取网站设置
$siteName = getSetting('site_name', '星系统 XingCMS');
$siteDescription = getSetting('site_description', '一个强大而灵活的内容管理系统');
$themeMode = getSetting('theme_mode', 'light');

// 获取分类和单页用于导航
$categories = getCategories();
$pages = getPages();

// 设置页面标题
$pageTitle = $category['name'] . ' - ' . $siteName;
$pageDescription = !empty($category['description']) ? $category['description'] : $category['name'] . '相关文章';
?>
<!DOCTYPE html>
<html lang="zh-CN" data-theme="<?php echo $themeMode; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- 头部区域 -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <?php
                    $siteLogo = getSetting('site_logo');
                    if (!empty($siteLogo)) {
                        echo '<img src="uploads/' . $siteLogo . '" alt="' . $siteName . '">';
                    } else {
                        echo '<div class="logo-placeholder">X</div>';
                    }
                    ?>
                    <h1><?php echo $siteName; ?></h1>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php">首页</a></li>
                        <?php foreach ($categories as $cat): ?>
                            <li><a href="category.php?id=<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $category_id ? 'class="active"' : ''; ?>>
                                <?php echo $cat['name']; ?>
                            </a></li>
                        <?php endforeach; ?>
                        <?php foreach ($pages as $page): ?>
                            <li><a href="page.php?slug=<?php echo $page['slug']; ?>"><?php echo $page['title']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                <div class="header-right">
                    <div class="auth-links">
                        <?php if (isLoggedIn()): ?>
                            <?php $user = getCurrentUser(); ?>
                            <a href="admin/dashboard.php" class="login">管理后台</a>
                            <a href="admin/logout.php" class="register">退出</a>
                        <?php else: ?>
                            <a href="admin/login.php" class="login">登录</a>
                            <a href="admin/login.php?action=register" class="register">注册</a>
                        <?php endif; ?>
                    </div>
                    <button class="theme-toggle" id="themeToggle">
                        <?php echo $themeMode === 'dark' ? '☀️' : '🌙'; ?>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- 面包屑导航 -->
        <nav class="breadcrumb" aria-label="面包屑导航">
            <ol>
                <li>
                    <a href="index.php">首页</a>
                </li>
                <li class="current" aria-current="page">
                    <?php echo $category['name']; ?>
                </li>
            </ol>
        </nav>

        <!-- 分类标题和描述 -->
        <div class="category-header">
            <h1 class="section-title"><?php echo $category['name']; ?></h1>
            <?php if (!empty($category['description'])): ?>
                <p class="category-description"><?php echo $category['description']; ?></p>
            <?php endif; ?>
        </div>

        <!-- 文章列表 -->
        <?php if (!empty($articles)): ?>
            <div class="articles-grid">
                <?php foreach ($articles as $article): ?>
                    <div class="article-card">
                        <div class="card-image">
                            <?php if (!empty($article['featured_image'])): ?>
                                <img src="uploads/<?php echo $article['featured_image']; ?>" alt="<?php echo $article['title']; ?>">
                            <?php else: ?>
                                <div class="image-placeholder"><?php echo mb_substr($article['title'], 0, 2); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h3><a href="article.php?id=<?php echo $article['id']; ?>"><?php echo $article['title']; ?></a></h3>
                            <p><?php echo $article['excerpt']; ?></p>
                            <div class="card-meta">
                                <span><?php echo $category['name']; ?></span>
                                <span><?php echo date('Y-m-d', strtotime($article['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-articles">
                <p>该分类下暂无文章。</p>
                <a href="index.php" class="btn">返回首页</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- 页脚区域 -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>关于我们</h3>
                    <ul>
                        <li><a href="page.php?slug=about">公司简介</a></li>
                        <li><a href="#">团队介绍</a></li>
                        <li><a href="#">发展历程</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>产品服务</h3>
                    <ul>
                        <li><a href="#">内容管理</a></li>
                        <li><a href="#">网站建设</a></li>
                        <li><a href="#">定制开发</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>帮助中心</h3>
                    <ul>
                        <li><a href="#">使用文档</a></li>
                        <li><a href="#">常见问题</a></li>
                        <li><a href="page.php?slug=contact">联系我们</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>关注我们</h3>
                    <ul>
                        <li><a href="#">微信公众号</a></li>
                        <li><a href="#">微博</a></li>
                        <li><a href="#">GitHub</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> <?php echo $siteName; ?> 版权所有</p>
            </div>
        </div>
    </footer>

    <script>
        // 主题切换功能
        const themeToggle = document.getElementById('themeToggle');
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            themeToggle.textContent = newTheme === 'dark' ? '☀️' : '🌙';
            
            // 保存主题设置到服务器
            fetch('admin/update_theme.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'theme=' + newTheme
            });
        });
    </script>
</body>
</html>