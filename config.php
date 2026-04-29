<?php
session_start();
// 无数据库！纯文件存储
define('DATA_PATH', __DIR__ . '/data/');
define('ADMIN_USER', 'admin'); // 后台账号
define('ADMIN_PWD', '123456'); // 后台密码

// 自动创建数据文件夹和文件
if (!is_dir(DATA_PATH)) mkdir(DATA_PATH, 0777, true);
$article_file = DATA_PATH . 'articles.json';
$message_file = DATA_PATH . 'messages.json';

// 初始化文章数据
if (!file_exists($article_file)) {
    $init = [
        ['type'=>'温室建设','title'=>'温室建设','subtitle'=>'标准化温室建设是高密度养殖成功的基础','content'=>'请在后台编辑内容'],
        ['type'=>'密度管理','title'=>'密度管理','subtitle'=>'分阶段控密，提高成活率','content'=>'请在后台编辑内容'],
        ['type'=>'饲料投喂','title'=>'饲料投喂','subtitle'=>'精细化投喂，促生长降成本','content'=>'请在后台编辑内容'],
        ['type'=>'菌群调控','title'=>'菌群调控','subtitle'=>'生态培菌，稳定水质','content'=>'请在后台编辑内容'],
        ['type'=>'疾病防治','title'=>'疾病防治','subtitle'=>'预防为主，科学防治','content'=>'请在后台编辑内容'],
        ['type'=>'水质应急','title'=>'水质应急','subtitle'=>'快速处理水质突发问题','content'=>'请在后台编辑内容'],
    ];
    file_put_contents($article_file, json_encode($init, JSON_UNESCAPED_UNICODE));
}
if (!file_exists($message_file)) file_put_contents($message_file, '[]');

// 后台登录验证
function checkLogin() {
    if (!isset($_SESSION['admin'])) {
        header('Location:login.php');
        exit;
    }
}
?>