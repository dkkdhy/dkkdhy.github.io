<?php
// admin/articles.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// 检查登录状态和权限
if (!isLoggedIn() || !hasPermission('author')) {
    redirect('login.php');
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$message = '';

// 处理文章操作
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $title = sanitizeInput($_POST['title']);
        $content = $_POST['content'];
        $excerpt = sanitizeInput($_POST['excerpt']);
        $category_id = intval($_POST['category_id']);
        $status = $_POST['status'];
        $slug = !empty($_POST['slug']) ? generateSlug($_POST['slug']) : generateSlug($title);
        
        // 检查slug是否唯一
        $checkStmt = $pdo->prepare("SELECT id FROM articles WHERE slug = ? AND id != ?");
        $checkStmt->execute([$slug, isset($_POST['id']) ? intval($_POST['id']) : 0]);
        if ($checkStmt->fetch()) {
            $slug = $slug . '-' . time();
        }
        
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO articles (title, content, excerpt, category_id, slug, status) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$title, $content, $excerpt, $category_id, $slug, $status])) {
                $message = '文章添加成功！';
            } else {
                $message = '文章添加失败！';
            }
        } else {
            $id = intval($_POST['id']);
            $stmt = $pdo->prepare("UPDATE articles SET title = ?, content = ?, excerpt = ?, category_id = ?, slug = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$title, $content, $excerpt, $category_id, $slug, $status, $id])) {
                $message = '文章更新成功！';
            } else {
                $message = '文章更新失败！';
            }
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = '文章删除成功！';
        } else {
            $message = '文章删除失败！';
        }
        $action = 'list';
    }
}

// 获取文章列表
if ($action === 'list') {
    $articles = $pdo->query("
        SELECT a.*, c.name as category_name 
        FROM articles a 
        LEFT JOIN categories c ON a.category_id = c.id 
        ORDER BY a.created_at DESC
    ")->fetchAll();
}

// 获取文章详情（编辑时）
if ($action === 'edit') {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch();
    
    if (!$article) {
        $message = '文章不存在！';
        $action = 'list';
    }
}

// 获取分类列表
$categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章管理 - 后台管理</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* 共用样式，与dashboard.php类似，这里简化显示 */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; display: flex; }
        .sidebar { width: 250px; background: #2c3e50; color: white; height: 100vh; position: fixed; }
        .sidebar-header { padding: 20px; border-bottom: 1px solid #34495e; }
        .sidebar-header h2 { color: #3498db; }
        .sidebar-menu { list-style: none; padding: 20px 0; }
        .sidebar-menu li { padding: 0; }
        .sidebar-menu a { display: block; padding: 12px 20px; color: #bdc3c7; text-decoration: none; transition: all 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #34495e; color: white; }
        .main-content { flex: 1; margin-left: 250px; padding: 20px; }
        .header { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        
        /* 文章管理特定样式 */
        .action-bar { margin-bottom: 20px; }
        .btn { display: inline-block; padding: 10px 15px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px; }
        .btn-danger { background: #e74c3c; }
        .btn-success { background: #2ecc71; }
        
        .table { width: 100%; background: white; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; }
        .table th, .table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ecf0f1; }
        .table th { background: #f8f9fa; font-weight: 600; }
        .table tr:last-child td { border-bottom: none; }
        
        .status-published { color: #2ecc71; }
        .status-draft { color: #e74c3c; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; }
        textarea.form-control { min-height: 200px; resize: vertical; }
        .form-actions { margin-top: 20px; }
        
        .alert { padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
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
            <li><a href="dashboard.php">控制台</a></li>
            <li><a href="articles.php" class="active">文章管理</a></li>
            <li><a href="categories.php">分类管理</a></li>
            <li><a href="pages.php">单页管理</a></li>
            <li><a href="settings.php">系统设置</a></li>
            <li><a href="logout.php">退出登录</a></li>
        </ul>
    </div>
    
    <!-- 主内容区 -->
    <div class="main-content">
        <div class="header">
            <h1>文章管理</h1>
            <div class="user-info">
                <span>欢迎，<?php echo $_SESSION['username']; ?></span>
                <a href="logout.php" class="btn btn-danger">退出</a>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($action === 'list'): ?>
            <div class="action-bar">
                <a href="?action=add" class="btn btn-success"><i class="fas fa-plus"></i> 添加文章</a>
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>标题</th>
                            <th>分类</th>
                            <th>状态</th>
                            <th>发布时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td><?php echo $article['title']; ?></td>
                                <td><?php echo $article['category_name'] ?: '未分类'; ?></td>
                                <td>
                                    <span class="status-<?php echo $article['status']; ?>">
                                        <?php echo $article['status'] === 'published' ? '已发布' : '草稿'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($article['created_at'])); ?></td>
                                <td>
                                    <a href="?action=edit&id=<?php echo $article['id']; ?>" class="btn">编辑</a>
                                    <form method="POST" action="?action=delete" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('确定删除这篇文章吗？')">删除</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <div class="action-bar">
                <a href="articles.php" class="btn">返回列表</a>
            </div>
            
            <form method="POST" action="?action=<?php echo $action; ?>">
                <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="title">文章标题 *</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?php echo isset($article) ? $article['title'] : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="slug">URL别名</label>
                    <input type="text" id="slug" name="slug" class="form-control" value="<?php echo isset($article) ? $article['slug'] : ''; ?>">
                    <small>留空将自动根据标题生成</small>
                </div>
                
                <div class="form-group">
                    <label for="category_id">分类</label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">未分类</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                <?php if (isset($article) && $article['category_id'] == $category['id']) echo 'selected'; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="excerpt">文章摘要</label>
                    <textarea id="excerpt" name="excerpt" class="form-control"><?php echo isset($article) ? $article['excerpt'] : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="content">文章内容 *</label>
                    <textarea id="content" name="content" class="form-control" required><?php echo isset($article) ? $article['content'] : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="status">状态</label>
                    <select id="status" name="status" class="form-control">
                        <option value="draft" <?php if (isset($article) && $article['status'] === 'draft') echo 'selected'; ?>>草稿</option>
                        <option value="published" <?php if (isset($article) && $article['status'] === 'published') echo 'selected'; ?>>已发布</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-success"><?php echo $action === 'add' ? '添加文章' : '更新文章'; ?></button>
                    <a href="articles.php" class="btn">取消</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
    
    <script>
        // 根据标题自动生成slug
        document.getElementById('title').addEventListener('blur', function() {
            const title = this.value;
            const slugField = document.getElementById('slug');
            
            if (title && !slugField.value) {
                // 简单的slug生成逻辑
                let slug = title.toLowerCase()
                    .replace(/[^\w\u4e00-\u9fa5]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                
                slugField.value = slug;
            }
        });
    </script>
</body>
</html>