<?php
// page.php - 单页详情页
require_once 'includes/config.php';
require_once 'includes/functions.php';

// 获取页面slug
$page_slug = isset($_GET['slug']) ? sanitizeInput($_GET['slug']) : '';

if (empty($page_slug)) {
    header('HTTP/1.0 404 Not Found');
    die('页面不存在');
}

// 获取页面详情
$stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published'");
$stmt->execute([$page_slug]);
$page = $stmt->fetch();

if (!$page) {
    header('HTTP/1.0 404 Not Found');
    die('页面不存在或尚未发布');
}

// 获取网站设置
$siteName = getSetting('site_name', '星系统 XingCMS');
$siteDescription = getSetting('site_description', '一个强大而灵活的内容管理系统');
$themeMode = getSetting('theme_mode', 'light');

// 获取分类和单页用于导航
$categories = getCategories();
$pages = getPages();

// 设置页面标题和描述
$pageTitle = !empty($page['meta_title']) ? $page['meta_title'] : $page['title'] . ' - ' . $siteName;
$pageDescription = !empty($page['meta_description']) ? $page['meta_description'] : (!empty($page['excerpt']) ? $page['excerpt'] : $page['title']);
?>
<!DOCTYPE html>
<html lang="zh-CN" data-theme="<?php echo $themeMode; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    
    <!-- Open Graph 元标签，用于社交媒体分享 -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL . '/page.php?slug=' . $page_slug; ?>">
    
    <!-- 规范URL，避免重复内容 -->
    <link rel="canonical" href="<?php echo SITE_URL . '/page.php?slug=' . $page_slug; ?>">
    
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* 单页特定样式 */
        .page-header {
            margin-bottom: var(--space-xl);
            text-align: center;
            padding: var(--space-lg) 0;
        }
        
        .page-title {
            font-size: var(--text-3xl);
            margin-bottom: var(--space-md);
            color: var(--text-color);
            line-height: 1.3;
        }
        
        .page-content {
            line-height: 1.8;
            font-size: var(--text-lg);
            color: var(--text-color);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .page-content h1,
        .page-content h2,
        .page-content h3,
        .page-content h4,
        .page-content h5,
        .page-content h6 {
            margin: var(--space-xl) 0 var(--space-lg);
            color: var(--text-color);
            line-height: 1.3;
        }
        
        .page-content h1 { font-size: var(--text-2xl); }
        .page-content h2 { font-size: var(--text-xl); }
        .page-content h3 { font-size: var(--text-lg); }
        .page-content h4 { font-size: var(--text-base); }
        .page-content h5 { font-size: var(--text-sm); }
        .page-content h6 { font-size: var(--text-xs); }
        
        .page-content p {
            margin-bottom: var(--space-lg);
        }
        
        .page-content blockquote {
            border-left: 4px solid var(--primary-color);
            padding-left: var(--space-lg);
            margin: var(--space-lg) 0;
            font-style: italic;
            color: var(--text-secondary);
        }
        
        .page-content img {
            max-width: 100%;
            height: auto;
            border-radius: var(--radius);
            box-shadow: var(--shadow-light);
            margin: var(--space-lg) 0;
        }
        
        .page-content ul,
        .page-content ol {
            margin: var(--space-lg) 0;
            padding-left: var(--space-xl);
        }
        
        .page-content li {
            margin-bottom: var(--space-sm);
        }
        
        .page-content table {
            width: 100%;
            border-collapse: collapse;
            margin: var(--space-lg) 0;
        }
        
        .page-content table th,
        .page-content table td {
            padding: var(--space-md);
            border: 1px solid var(--border-color);
            text-align: left;
        }
        
        .page-content table th {
            background-color: var(--bg-secondary);
            font-weight: var(--font-semibold);
        }
        
        .page-content pre {
            background-color: var(--bg-secondary);
            padding: var(--space-lg);
            border-radius: var(--radius);
            overflow-x: auto;
            margin: var(--space-lg) 0;
        }
        
        .page-content code {
            background-color: var(--bg-secondary);
            padding: 2px 6px;
            border-radius: var(--radius-sm);
            font-family: 'Courier New', monospace;
        }
        
        .page-content pre code {
            background: none;
            padding: 0;
        }
        
        .page-footer {
            margin-top: var(--space-xxl);
            padding-top: var(--space-lg);
            border-top: 1px solid var(--border-color);
            text-align: center;
        }
        
        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-lg);
            margin: var(--space-xl) 0;
        }
        
        .contact-item {
            background-color: var(--bg-secondary);
            padding: var(--space-lg);
            border-radius: var(--radius);
            text-align: center;
            transition: var(--transition);
        }
        
        .contact-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }
        
        .contact-item .icon {
            font-size: 2rem;
            margin-bottom: var(--space-md);
            color: var(--primary-color);
        }
        
        .contact-form {
            background-color: var(--bg-secondary);
            padding: var(--space-xl);
            border-radius: var(--radius);
            margin-top: var(--space-xl);
        }
        
        .form-group {
            margin-bottom: var(--space-lg);
        }
        
        .form-label {
            display: block;
            margin-bottom: var(--space-sm);
            font-weight: var(--font-medium);
            color: var(--text-color);
        }
        
        .form-control {
            width: 100%;
            padding: var(--space-md);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: var(--text-base);
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-lg);
            margin: var(--space-xl) 0;
        }
        
        .team-member {
            background-color: var(--bg-secondary);
            border-radius: var(--radius);
            overflow: hidden;
            text-align: center;
            transition: var(--transition);
        }
        
        .team-member:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }
        
        .team-member img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .team-member-info {
            padding: var(--space-lg);
        }
        
        .team-member-name {
            font-size: var(--text-lg);
            font-weight: var(--font-semibold);
            margin-bottom: var(--space-xs);
        }
        
        .team-member-position {
            color: var(--primary-color);
            margin-bottom: var(--space-md);
        }
        
        .team-member-bio {
            color: var(--text-secondary);
            font-size: var(--text-sm);
        }
        
        @media (max-width: 768px) {
            .page-title {
                font-size: var(--text-2xl);
            }
            
            .contact-form {
                padding: var(--space-lg);
            }
            
            .team-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
                        <?php foreach ($categories as $category): ?>
                            <li><a href="category.php?id=<?php echo $category['id']; ?>"><?php echo $category['name']; ?></a></li>
                        <?php endforeach; ?>
                        <?php foreach ($pages as $page_item): ?>
                            <li><a href="page.php?slug=<?php echo $page_item['slug']; ?>" <?php echo $page_item['slug'] == $page_slug ? 'class="active"' : ''; ?>>
                                <?php echo $page_item['title']; ?>
                            </a></li>
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

    <!-- 页面内容 -->
    <div class="container">
        <!-- 面包屑导航 -->
        <nav class="breadcrumb" aria-label="面包屑导航">
            <ol>
                <li>
                    <a href="index.php">首页</a>
                </li>
                <li class="current" aria-current="page">
                    <?php echo $page['title']; ?>
                </li>
            </ol>
        </nav>
        
        <!-- 页面头部 -->
        <article class="page">
            <header class="page-header">
                <h1 class="page-title"><?php echo htmlspecialchars($page['title']); ?></h1>
            </header>
            
            <!-- 页面内容 -->
            <div class="page-content">
                <?php 
                // 直接输出HTML内容
                echo $page['content']; 
                ?>
                
                <!-- 如果是联系我们页面，显示联系表单 -->
                <?php if ($page_slug == 'contact'): ?>
                    <div class="contact-form">
                        <h3>发送消息</h3>
                        <form id="contactForm" method="POST">
                            <div class="form-group">
                                <label for="name" class="form-label">姓名</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">邮箱</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="subject" class="form-label">主题</label>
                                <input type="text" id="subject" name="subject" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="message" class="form-label">消息内容</label>
                                <textarea id="message" name="message" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">发送消息</button>
                        </form>
                    </div>
                    
                    <script>
                        document.getElementById('contactForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            alert('感谢您的留言！我们会尽快回复您。');
                            this.reset();
                        });
                    </script>
                <?php endif; ?>
            </div>
            
            <!-- 页面底部 -->
            <footer class="page-footer">
                <a href="javascript:history.back()" class="btn btn-outline">返回</a>
            </footer>
        </article>
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
        
        // 图片灯箱效果
        document.querySelectorAll('.page-content img').forEach(img => {
            img.style.cursor = 'zoom-in';
            img.addEventListener('click', function() {
                const overlay = document.createElement('div');
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.8);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                    cursor: zoom-out;
                `;
                
                const enlargedImg = document.createElement('img');
                enlargedImg.src = this.src;
                enlargedImg.alt = this.alt;
                enlargedImg.style.cssText = `
                    max-width: 90%;
                    max-height: 90%;
                    object-fit: contain;
                    border-radius: 8px;
                `;
                
                overlay.appendChild(enlargedImg);
                document.body.appendChild(overlay);
                
                overlay.addEventListener('click', function() {
                    document.body.removeChild(overlay);
                });
            });
        });
        
        // 平滑滚动到锚点
        document.querySelectorAll('.page-content a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>