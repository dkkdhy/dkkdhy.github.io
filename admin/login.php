<?php
// admin/login.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// 如果用户已登录，重定向到后台
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$action = isset($_GET['action']) ? $_GET['action'] : 'login';
$error = '';
$success = '';

// 处理登录/注册表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    if ($action === 'login') {
        // 登录处理
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            redirect('dashboard.php');
        } else {
            $error = '用户名或密码错误！';
        }
    } else {
        // 注册处理
        $email = sanitizeInput($_POST['email']);
        $confirm_password = $_POST['confirm_password'];
        
        // 验证输入
        if (empty($username) || empty($password) || empty($email)) {
            $error = '请填写所有必填字段！';
        } elseif ($password !== $confirm_password) {
            $error = '两次输入的密码不一致！';
        } else {
            // 检查用户名是否已存在
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = '用户名或邮箱已存在！';
            } else {
                // 创建新用户
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'author')");
                if ($stmt->execute([$username, $hashed_password, $email])) {
                    $success = '注册成功！请登录。';
                    $action = 'login'; // 切换到登录表单
                } else {
                    $error = '注册失败，请稍后重试！';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $action === 'login' ? '登录' : '注册'; ?> - 后台管理</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus {
            border-color: #667eea;
            outline: none;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #5a6fd8;
        }
        
        .switch-action {
            text-align: center;
            margin-top: 20px;
        }
        
        .switch-action a {
            color: #667eea;
            text-decoration: none;
        }
        
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #ffe6e6;
            color: #d63031;
            border: 1px solid #ff7675;
        }
        
        .alert-success {
            background: #e6fffa;
            color: #00b894;
            border: 1px solid #55efc4;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>星系统 CMS - <?php echo $action === 'login' ? '登录' : '注册'; ?></h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($action === 'login'): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">用户名</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">密码</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">登录</button>
            </form>
            <div class="switch-action">
                没有账户？ <a href="?action=register">立即注册</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">用户名</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">邮箱</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">密码</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">确认密码</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit">注册</button>
            </form>
            <div class="switch-action">
                已有账户？ <a href="?action=login">立即登录</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>