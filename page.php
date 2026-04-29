<?php 
include 'config.php';
$type = $_GET['type'] ?? '温室建设';
$articles = json_decode(file_get_contents(DATA_PATH.'articles.json'), true);
$current = array_filter($articles, fn($item) => $item['type'] == $type);
$current = reset($current);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$current['title']?> - 中华鳖养殖技术平台</title>
    <style>
        * {margin:0;padding:0;box-sizing:border-box;font-family: "Microsoft YaHei",sans-serif;}
        :root {--main:#0F4C81;--green:#4ECDC4;--bg:#f9f9f9;--text:#333;--gray:#666;}
        body{background:var(--bg);color:var(--text);line-height:1.8;}
        .header{position:fixed;top:0;width:100%;height:70px;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,0.1);z-index:999;}
        .nav{max-width:1200px;margin:0 auto;height:70px;display:flex;align-items:center;justify-content:space-between;padding:0 20px;}
        .logo{font-size:20px;font-weight:bold;color:var(--main);}
        .menu a{margin-left:25px;text-decoration:none;color:#333;font-weight:500;}
        .menu a:hover{color:var(--main);}
        .page-header{margin-top:70px;padding:60px 20px;background:var(--main);color:#fff;text-align:center;}
        .container{max-width:1000px;margin:-40px auto 0;padding:60px;background:#fff;border-radius:10px;box-shadow:0 0 20px rgba(0,0,0,0.05);position:relative;z-index:10;}
        .content{color:var(--gray);font-size:16px;line-height:2;white-space:pre-line;}
        .footer{background:var(--main);color:#fff;text-align:center;padding:30px 0;margin-top:50px;}
        @media(max-width:768px){.menu{display:none;}}
    </style>
</head>
<body>
<div class="header">
    <div class="nav">
        <div class="logo">中华鳖温室高密度养殖</div>
        <div class="menu">
            <a href="index.php">首页</a>
            <a href="page.php?type=温室建设">温室建设</a>
            <a href="page.php?type=密度管理">密度管理</a>
            <a href="page.php?type=饲料投喂">饲料投喂</a>
            <a href="page.php?type=菌群调控">菌群调控</a>
            <a href="page.php?type=疾病防治">疾病防治</a>
            <a href="page.php?type=水质应急">水质应急</a>
        </div>
    </div>
</div>
<div class="page-header">
    <h1><?=$current['title']?></h1>
    <p><?=$current['subtitle']?></p>
</div>
<div class="container">
    <div class="content"><?=$current['content']?></div>
</div>
<div class="footer"><p>© 2025 中华鳖温室高密度科学养殖技术平台</p></div>
</body>
</html>