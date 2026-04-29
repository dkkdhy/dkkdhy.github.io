<?php include '../config.php'; checkLogin();
$articles = json_decode(file_get_contents(DATA_PATH.'articles.json'), true);
?>
<!DOCTYPE html>
<meta charset="UTF-8">
<title>管理后台</title>
<style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:微软雅黑;}
    .header{height:70px;background:#0F4C81;color:#fff;line-height:70px;padding:0 30px;}
    .main{padding:30px;}
    .item{padding:15px;background:#fff;margin:10px 0;border-radius:5px;box-shadow:0 2px 5px #eee;display:flex;justify-content:space-between;align-items:center;}
    a{padding:8px 15px;background:#0F4C81;color:#fff;text-decoration:none;border-radius:3px;}
</style>
<div class="header"><h2>文章管理</h2></div>
<div class="main">
    <?php foreach($articles as $k=>$v){ ?>
        <div class="item">
            <span><?=$v['type']?></span>
            <a href="edit.php?id=<?=$k?>">编辑内容</a>
        </div>
    <?php } ?>
    <br>
    <a href="message.php">查看留言</a>
    <a href="logout.php" style="background:red;margin-left:10px;">退出登录</a>
</div>