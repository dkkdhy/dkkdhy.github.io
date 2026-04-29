<?php include '../config.php'; checkLogin();
$id = $_GET['id'];
$articles = json_decode(file_get_contents(DATA_PATH.'articles.json'), true);
if($_POST){
    $articles[$id]['subtitle'] = $_POST['subtitle'];
    $articles[$id]['content'] = $_POST['content'];
    file_put_contents(DATA_PATH.'articles.json', json_encode($articles, JSON_UNESCAPED_UNICODE));
    echo "<script>alert('保存成功');location.href='index.php';</script>";
}
?>
<!DOCTYPE html>
<meta charset="UTF-8">
<style>
    body{padding:20px;font-family:微软雅黑;}
    input,textarea{width:100%;margin:10px 0;padding:10px;border:1px solid #ddd;border-radius:5px;}
    textarea{min-height:300px;}
    button{padding:10px 30px;background:#0F4C81;color:#fff;border:0;border-radius:5px;cursor:pointer;}
</style>
<form method="post">
    副标题：<input name="subtitle" value="<?=$articles[$id]['subtitle']?>">
    内容：<textarea name="content"><?=$articles[$id]['content']?></textarea>
    <button type="submit">保存内容</button>
</form>