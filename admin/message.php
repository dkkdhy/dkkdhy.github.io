<?php include '../config.php'; checkLogin();
$messages = json_decode(file_get_contents(DATA_PATH.'messages.json'), true);
?>
<!DOCTYPE html>
<meta charset="UTF-8">
<h3>用户留言</h3>
<?php foreach($messages as $m){ ?>
    <p><?=$m['time']?> | <?=$m['name']?> | <?=$m['phone']?> | <?=$m['content']?></p><hr>
<?php } ?>
<a href="index.php">返回</a>