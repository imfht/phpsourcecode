<?php
/**
 * 调试开关
 * 项目正式部署后请设置为false
 */
define ( 'APP_DEBUG', false);

//从URL获取SESSION编号
ini_set("session.use_cookies",0);
ini_set("session.use_trans_sid",1);
if($_REQUEST['session_id']) {
    session_id($_REQUEST['session_id']);
    session_start();
}

//调用Application/Api应用
$_GET['m'] = 'App';
define ( 'APP_PATH', './App/' );
define ( 'RUNTIME_PATH', './Runtime/' );
require './ThinkPHP/ThinkPHP.php';