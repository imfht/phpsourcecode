<?php

header("Content-type: text/html; charset=utf-8");
if (!defined('APP_IN')) exit('Access Denied');
$m_name = '生成静态';
$ac_arr = array('create'=>'一键更新');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl ->assign('weburl',WEB_PATH);
$tpl ->assign('mod_name',$m_name);
$tpl ->assign('ac_arr',$ac_arr);
$tpl ->assign('ac',$ac);
if ($ac == 'create') {
$action = isset($_GET['action']) ?$_GET['action'] : '';
if ($action!=''){
$step = isset($_GET['step']) ?$_GET['step'] : 1;
if($step==1){
html_index();
showmsg('更新首页成功','admin.php?m=create_html_all&a=create&ation=create&step=2');
exit;
}
elseif($step==2){
showmsg('更新产品成功','admin.php?m=create_html_all&step=3');
exit;
}
elseif($step==3){
showmsg('更新新闻成功','admin.php?m=create_html_all&step=4');
exit;
}
elseif($step==3){
showmsg('更新单页成功','admin.php?m=create_html_all');
exit;
}
}
else{
$tpl ->display("admin/create_html_all.html");
}
}

?>