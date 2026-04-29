<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>中华鳖温室高密度科学养殖技术平台</title>
    <style>
        * {margin:0;padding:0;box-sizing:border-box;font-family: "Microsoft YaHei",sans-serif;scroll-behavior:smooth;}
        :root {--main:#0F4C81;--green:#4ECDC4;--bg:#f9f9f9;--text:#333;}
        body{background:var(--bg);color:var(--text);line-height:1.8;}
        .header{position:fixed;top:0;width:100%;height:70px;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,0.1);z-index:999;}
        .nav{max-width:1200px;margin:0 auto;height:70px;display:flex;align-items:center;justify-content:space-between;padding:0 20px;}
        .logo{font-size:20px;font-weight:bold;color:var(--main);}
        .menu a{margin-left:25px;text-decoration:none;color:#333;font-weight:500;}
        .menu a:hover{color:var(--main);}
        .banner{margin-top:70px;padding:120px 20px;background:linear-gradient(rgba(15,76,129,0.8),rgba(15,76,129,0.8)),url(https://picsum.photos/id/1042/1920/1080) center/cover;color:#fff;text-align:center;}
        .banner h1{font-size:40px;margin-bottom:20px;}
        .banner p{max-width:900px;margin:0 auto 30px;font-size:18px;}
        .container{max-width:1200px;margin:50px auto;padding:0 20px;}
        .title{text-align:center;font-size:30px;color:var(--main);margin-bottom:40px;}
        .card-box{display:grid;grid-template-columns:repeat(3,1fr);gap:30px;}
        .card{background:#fff;padding:30px;border-radius:10px;box-shadow:0 3px 15px rgba(0,0,0,0.05);text-align:center;text-decoration:none;color:var(--text);transition:0.3s;}
        .card:hover{transform:translateY(-5px);color:var(--main);}
        .footer{background:var(--main);color:#fff;text-align:center;padding:30px 0;margin-top:50px;}
        @media(max-width:768px){.menu{display:none;}.card-box{grid-template-columns:1fr;}}
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
<div class="banner">
    <h1>科学高密度养殖 · 提质增产降本</h1>
    <p>专注中华鳖温室标准化养殖技术，一站式解决温室建设、密度管控、饲料投喂、菌群调控、疾病防治、水质应急全流程难题</p>
</div>
<div class="container">
    <h2 class="title">六大核心技术</h2>
    <div class="card-box">
        <a href="page.php?type=温室建设" class="card"><h3>温室建设</h3><p>标准化搭建+设备布局</p></a>
        <a href="page.php?type=密度管理" class="card"><h3>密度管理</h3><p>分阶段控密·防应激</p></a>
        <a href="page.php?type=饲料投喂" class="card"><h3>饲料投喂</h3><p>精细化投喂·促生长</p></a>
        <a href="page.php?type=菌群调控" class="card"><h3>菌群调控</h3><p>生态培菌·稳水质</p></a>
        <a href="page.php?type=疾病防治" class="card"><h3>疾病防治</h3><p>预防为主·科学用药</p></a>
        <a href="page.php?type=水质应急" class="card"><h3>水质应急</h3><p>快速急救·水质修复</p></a>
    </div>
</div>
<div class="footer"><p>© 2025 中华鳖温室高密度科学养殖技术平台</p></div>
</body>
</html>