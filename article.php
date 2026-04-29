<?php
// article.php - 文章详情页
require_once 'includes/config.php';
require_once 'includes/functions.php';

// 获取文章ID
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($article_id <= 0) {
    header('HTTP/1.0 404 Not Found');
    die('文章不存在');
}

// 获取文章详情
$stmt = $pdo->prepare("
    SELECT a.*, c.name as category_name, c.slug as category_slug 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    WHERE a.id = ? AND a.status = 'published'
");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if (!$article) {
    header('HTTP/1.0 404 Not Found');
    die('文章不存在或尚未发布');
}

// 增加文章浏览量
$pdo->prepare("UPDATE articles SET view_count = view_count + 1 WHERE id = ?")->execute([$article_id]);

// 获取相关文章 (同一分类)
$related_stmt = $pdo->prepare("
    SELECT id, title, excerpt, created_at 
    FROM articles 
    WHERE category_id = ? AND id != ? AND status = 'published' 
    ORDER BY created_at DESC 
    LIMIT 4
");
$related_stmt->execute([$article['category_id'], $article_id]);
$related_articles = $related_stmt->fetchAll();

// 获取网站设置
$siteName = getSetting('site_name', '星系统 XingCMS');
$siteDescription = getSetting('site_description', '一个强大而灵活的内容管理系统');
$themeMode = getSetting('theme_mode', 'light');

// 获取分类和单页用于导航
$categories = getCategories();
$pages = getPages();

// 设置页面标题和描述
$pageTitle = $article['title'] . ' - ' . $siteName;
$pageDescription = !empty($article['excerpt']) ? $article['excerpt'] : $article['title'];
$pageKeywords = ''; // 可以根据需要添加关键词

// 如果有meta_title则使用，否则使用文章标题
if (!empty($article['meta_title'])) {
    $pageTitle = $article['meta_title'];
}

// 如果有meta_description则使用，否则使用摘要
if (!empty($article['meta_description'])) {
    $pageDescription = $article['meta_description'];
}
?>
<!DOCTYPE html>
<html lang="zh-CN" data-theme="<?php echo $themeMode; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <?php if (!empty($pageKeywords)): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords); ?>">
    <?php endif; ?>
    
    <!-- Open Graph 元标签，用于社交媒体分享 -->
    <meta property="og:title" content="<?php echo htmlspecialchars($article['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo SITE_URL . '/article.php?id=' . $article_id; ?>">
    <?php if (!empty($article['featured_image'])): ?>
    <meta property="og:image" content="<?php echo SITE_URL . '/uploads/' . $article['featured_image']; ?>">
    <?php endif; ?>
    
    <!-- 规范URL，避免重复内容 -->
    <link rel="canonical" href="<?php echo SITE_URL . '/article.php?id=' . $article_id; ?>">
    
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* 文章详情页特定样式 */
        .article-header {
            margin-bottom: var(--space-xl);
            text-align: center;
            padding: var(--space-lg) 0;
        }
        
        .article-title {
            font-size: var(--text-3xl);
            margin-bottom: var(--space-md);
            color: var(--text-color);
            line-height: 1.3;
        }
        
        .article-meta {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: var(--space-lg);
            color: var(--text-secondary);
            font-size: var(--text-sm);
            margin-bottom: var(--space-lg);
        }
        
        .article-meta span {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
        }
        
        .article-meta a {
            color: var(--text-secondary);
            transition: var(--transition);
        }
        
        .article-meta a:hover {
            color: var(--primary-color);
        }
        
        .article-featured-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: var(--radius);
            margin-bottom: var(--space-xl);
            box-shadow: var(--shadow);
        }
        
        .article-content {
            line-height: 1.8;
            font-size: var(--text-lg);
            color: var(--text-color);
        }
        
        .article-content h1,
        .article-content h2,
        .article-content h3,
        .article-content h4,
        .article-content h5,
        .article-content h6 {
            margin: var(--space-xl) 0 var(--space-lg);
            color: var(--text-color);
            line-height: 1.3;
        }
        
        .article-content h1 { font-size: var(--text-2xl); }
        .article-content h2 { font-size: var(--text-xl); }
        .article-content h3 { font-size: var(--text-lg); }
        .article-content h4 { font-size: var(--text-base); }
        .article-content h5 { font-size: var(--text-sm); }
        .article-content h6 { font-size: var(--text-xs); }
        
        .article-content p {
            margin-bottom: var(--space-lg);
        }
        
        .article-content blockquote {
            border-left: 4px solid var(--primary-color);
            padding-left: var(--space-lg);
            margin: var(--space-lg) 0;
            font-style: italic;
            color: var(--text-secondary);
        }
        
        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: var(--radius);
            box-shadow: var(--shadow-light);
            margin: var(--space-lg) 0;
        }
        
        .article-content ul,
        .article-content ol {
            margin: var(--space-lg) 0;
            padding-left: var(--space-xl);
        }
        
        .article-content li {
            margin-bottom: var(--space-sm);
        }
        
        .article-content table {
            width: 100%;
            border-collapse: collapse;
            margin: var(--space-lg) 0;
        }
        
        .article-content table th,
        .article-content table td {
            padding: var(--space-md);
            border: 1px solid var(--border-color);
            text-align: left;
        }
        
        .article-content table th {
            background-color: var(--bg-secondary);
            font-weight: var(--font-semibold);
        }
        
        .article-content pre {
            background-color: var(--bg-secondary);
            padding: var(--space-lg);
            border-radius: var(--radius);
            overflow-x: auto;
            margin: var(--space-lg) 0;
        }
        
        .article-content code {
            background-color: var(--bg-secondary);
            padding: 2px 6px;
            border-radius: var(--radius-sm);
            font-family: 'Courier New', monospace;
        }
        
        .article-content pre code {
            background: none;
            padding: 0;
        }
        
        .article-footer {
            margin-top: var(--space-xxl);
            padding-top: var(--space-lg);
            border-top: 1px solid var(--border-color);
        }
        
        .article-tags {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-sm);
            margin-bottom: var(--space-lg);
        }
        
        .tag {
            background-color: var(--bg-secondary);
            padding: var(--space-xs) var(--space-md);
            border-radius: 20px;
            font-size: var(--text-sm);
            color: var(--text-secondary);
            transition: var(--transition);
        }
        
        .tag:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .article-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: var(--space-md);
        }
        
        .social-share {
            display: flex;
            gap: var(--space-sm);
        }
        
        .share-btn {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            padding: var(--space-sm) var(--space-md);
            background-color: var(--bg-secondary);
            border-radius: var(--radius);
            color: var(--text-color);
            text-decoration: none;
            transition: var(--transition);
            font-size: var(--text-sm);
        }
        
        .share-btn:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .related-articles {
            margin-top: var(--space-xxl);
        }
        
        .related-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: var(--space-lg);
            margin-top: var(--space-lg);
        }
        
        @media (max-width: 768px) {
            .article-title {
                font-size: var(--text-2xl);
            }
            
            .related-grid {
                grid-template-columns: 1fr;
            }
            
            .article-actions {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .social-share {
                width: 100%;
                justify-content: space-between;
            }
        }
        
        .breadcrumb {
            margin-bottom: var(--space-lg);
            font-size: var(--text-sm);
            color: var(--text-secondary);
        }
        
        .breadcrumb a {
            color: var(--text-secondary);
            transition: var(--transition);
        }
        
        .breadcrumb a:hover {
            color: var(--primary-color);
        }
        
        .breadcrumb span {
            margin: 0 var(--space-xs);
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

    <!-- 文章内容 -->
    <div class="container">
        <!-- 面包屑导航 -->
        <!-- 面包屑导航 -->
<nav class="breadcrumb" aria-label="面包屑导航">
    <ol>
        <li>
            <a href="index.php">首页</a>
        </li>
        <?php if (!empty($article['category_name'])): ?>
            <li>
                <a href="category.php?id=<?php echo $article['category_id']; ?>">
                    <?php echo $article['category_name']; ?>
                </a>
            </li>
        <?php endif; ?>
        <li class="current" aria-current="page">
            <?php echo $article['title']; ?>
        </li>
    </ol>
</nav>
        
        <!-- 文章头部 -->
        <article class="article">
            <header class="article-header">
                <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                
                <div class="article-meta">
                    <?php if (!empty($article['category_name'])): ?>
                        <span>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M3.5 2a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0v-5a.5.5 0 0 1 .5-.5zm0 8a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0v-5a.5.5 0 0 1 .5-.5zm5-8a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0v-5a.5.5 0 0 1 .5-.5zm0 8a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0v-5a.5.5 0 0 1 .5-.5zm5-8a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0v-5a.5.5 0 0 1 .5-.5zm0 8a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0v-5a.5.5 0 0 1 .5-.5z"/>
                            </svg>
                            <a href="category.php?id=<?php echo $article['category_id']; ?>"><?php echo $article['category_name']; ?></a>
                        </span>
                    <?php endif; ?>
                    
                    <span>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                        </svg>
                        发布于 <?php echo date('Y年m月d日', strtotime($article['created_at'])); ?>
                    </span>
                    
                    <span>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                        </svg>
                        阅读 <?php echo $article['view_count'] + 1; ?> 次
                    </span>
                </div>
                
                <?php if (!empty($article['featured_image'])): ?>
                    <img src="uploads/<?php echo $article['featured_image']; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="article-featured-image">
                <?php endif; ?>
            </header>
            
            <!-- 文章内容 -->
            <div class="article-content">
                <?php 
                // 直接输出HTML内容
                echo $article['content']; 
                ?>
            </div>
            
            <!-- 文章底部 -->
            <footer class="article-footer">
                <!-- 标签区域（如果有的话） -->
                <!-- <div class="article-tags">
                    <a href="#" class="tag">PHP</a>
                    <a href="#" class="tag">CMS</a>
                    <a href="#" class="tag">Web开发</a>
                </div> -->
                
                <div class="article-actions">
                    <!-- 分享按钮 -->
                    <div class="social-share">
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/article.php?id=' . $article_id); ?>&text=<?php echo urlencode($article['title']); ?>" 
                           target="_blank" class="share-btn">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/>
                            </svg>
                            Twitter
                        </a>
                        
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/article.php?id=' . $article_id); ?>" 
                           target="_blank" class="share-btn">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
                            </svg>
                            Facebook
                        </a>
                        
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(SITE_URL . '/article.php?id=' . $article_id); ?>" 
                           target="_blank" class="share-btn">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/>
                            </svg>
                            LinkedIn
                        </a>
                    </div>
                    
                    <!-- 返回按钮 -->
                    <a href="javascript:history.back()" class="btn btn-outline">返回</a>
                </div>
            </footer>
        </article>
        
        <!-- 相关文章 -->
        <?php if (!empty($related_articles)): ?>
            <section class="related-articles">
                <h2 class="section-title">相关文章</h2>
                <div class="related-grid">
                    <?php foreach ($related_articles as $related): ?>
                        <div class="article-card">
                            <div class="card-image">
                                <div class="image-placeholder"><?php echo mb_substr($related['title'], 0, 2); ?></div>
                            </div>
                            <div class="card-content">
                                <h3><a href="article.php?id=<?php echo $related['id']; ?>"><?php echo htmlspecialchars($related['title']); ?></a></h3>
                                <p><?php echo htmlspecialchars($related['excerpt']); ?></p>
                                <div class="card-meta">
                                    <span><?php echo $article['category_name']; ?></span>
                                    <span><?php echo date('Y-m-d', strtotime($related['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
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
        
        // 图片灯箱效果
        document.querySelectorAll('.article-content img').forEach(img => {
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
        document.querySelectorAll('.article-content a[href^="#"]').forEach(anchor => {
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