<?php

if (!defined('APP_IN')) exit('Access Denied');
$mod_name = '模版管理';
$ac_arr = array('list'=>'模版列表','add'=>'添加模版','edit'=>'编辑模版');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
$tpl ->assign('mod_name',$mod_name);
$tpl ->assign('ac_arr',$ac_arr);
$tpl ->assign('ac',$ac);
$dirname = isset($_REQUEST['dir']) &&trim($_REQUEST['dir']) ?str_replace(array('..\\','../','./','.\\'),'',trim($_REQUEST['dir'])) : '';
if ($ac == 'list') {
if (!empty($dirname)) {
$dir = "templates/default/".$dirname ."/";
if (is_dir($dir)) {
if ($dh = opendir($dir)) {
$i = 0;
while (($file = readdir($dh)) !== false) {
$filetype = filetype($dir .$file);
if ($filetype != "dir"and $file != "config.php") {
$list[$i]['name'] = $file;
$configflie = $dir ."config.php";
if (file_exists($configflie)) {
$config = require($configflie);
if (!empty($config['file_explan'][$file])) {
$list[$i]['detail'] = $config['file_explan'][$file];
}
}
}
$i++;
}
closedir($dh);
}
}
$tpl ->assign('templateslist',$list);
$tpl ->assign('dir',$dirname);
$tpl ->display('admin/templates_list.html');
}
exit;
}
elseif ($ac == 'bulkedit') {
if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
foreach ($_POST['bulkid'] as $k =>$v) {
$rs = $db ->row_update('templates',array('name'=>$_POST['name'][$v]),"id=".intval($v));
}
}
elseif ($ac == 'add'||$ac == 'edit') {
if (submitcheck('a')) {
if ($ac == 'add') {
$filename = "templates/default/".$dirname ."/".$_POST['filename'];
$extension = substr(strrchr($filename,'.'),1);
if (file_exists($filename)) {
showmsg('文件已存在');
}else {
if ($extension != 'html') {
showmsg('文件名称不合法');
}else {
$code = stripslashes($_POST['code']);
file_put_contents($filename,$code);
}
}
}else {
$file = "templates/default/".$dirname ."/".$_POST['filename'];
$extension = substr(strrchr($file,'.'),1);
if (file_exists($filename)) {
showmsg('文件已存在');
}else {
if ($extension != 'html') {
showmsg('文件名称不合法');
}else {
$code = stripslashes($_POST['code']);
$code = stripslashes($code);
$code = preg_replace("/##textarea/i","<textarea",$code);
$code = preg_replace("/##\/textarea/i","</textarea",$code);
$code = preg_replace("/##form/i","<form",$code);
$code = preg_replace("/##\/form/i","</form",$code);
$fp = fopen($file,"w");
fwrite($fp,$code);
fclose($fp);
}
}
}
}
else {
if (empty($_GET['filename'])) {
$data = array('name'=>'','code'=>'');
}else {
$file = "templates/default/".$dirname ."/".$_GET['filename'];
if (!file_exists($file)) {
showmsg('文件不存在',-1);
}
$data['code'] = file_get_contents($file);
$data['code'] = preg_replace("#<textarea#i","##textarea",$data['code']);
$data['code'] = preg_replace("#</textarea#i","##/textarea",$data['code']);
$data['code'] = preg_replace("#<form#i","##form",$data['code']);
$data['code'] = preg_replace("#</form#i","##/form",$data['code']);
$data['filename'] = $_GET['filename'];
}
$tpl ->assign('dir',$dirname);
$tpl ->assign('templates',$data);
$tpl ->assign('ac',$ac);
$tpl ->display('admin/add_templates.html');
exit;
}
}
else {
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac] .'成功',ADMIN_PAGE ."?m=templates&a=list&dir=".$dirname);
?>