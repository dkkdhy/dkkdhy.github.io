<?php 
session_start();
include '../config.php';
if($_POST){
    if($_POST['user']==ADMIN_USER && $_POST['pwd']==ADMIN_PWD){
        $_SESSION['admin'] = true;
        header('Location:index.php');
    }else{
        echo "<script>alert('账号密码错误');</script>";
    }
}
?>
<!DOCTYPE html>
<meta charset="UTF-8">
<title>后台登录</title>
<style>
    body{background:#f5f5f5;font-family:微软雅黑;}
    .box{width:400px;margin:150px auto;background:#fff;padding:40px;border-radius:10px;box-shadow:0 0 20px #ddd;}
    h2{text-align:center;color:#0F4C81;margin-bottom:30px;}
    input{width:100%;height:45px;margin:10px 0;padding:0 15px;border:1px solid #ddd;border-radius:5px;}
    button{width:100%;height:45px;background:#0F4C81;color:#fff;border:0;border-radius:5px;cursor:pointer;}
</style>
<div class="box">
    <h2>后台登录</h2>
    <form method="post">
        <input name="user" placeholder="账号" required>
        <input name="pwd" type="password" placeholder="密码" required>
        <button type="submit">登录</button>
    </form>
</div>