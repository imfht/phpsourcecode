<?php

if (!defined('APP_IN')) exit('Access Denied');
include('json.php');
$php_path = dirname(__FILE__) .'/';
$php_url = dirname($_SERVER['PHP_SELF']) .'/';
$save_path ="upload/";
$save_url ='/upload/';
$ext_arr = array(
'image'=>array( 'jpg','jpeg','png','bmp')
);
$max_size = 1000000;
if (!empty($_FILES['imgFile']['error'])) {
switch($_FILES['imgFile']['error']){
case '1':
$error = '超过php.ini允许的大小。';
break;
case '2':
$error = '超过表单允许的大小。';
break;
case '3':
$error = '图片只有部分被上传。';
break;
case '4':
$error = '请选择图片。';
break;
case '6':
$error = '找不到临时目录。';
break;
case '7':
$error = '写文件到硬盘出错。';
break;
case '8':
$error = 'File upload stopped by extension。';
break;
case '999':
default:
$error = '未知错误。';
}
alert($error);
}
if (empty($_FILES) === false) {
$file_name = $_FILES['imgFile']['name'];
$tmp_name = $_FILES['imgFile']['tmp_name'];
$file_size = $_FILES['imgFile']['size'];
if (!$file_name) {
alert("请选择文件。");
}
if (@is_dir($save_path) === false) {
alert("上传目录不存在。");
}
if (@is_writable($save_path) === false) {
alert("上传目录没有写权限。");
}
if (@is_uploaded_file($tmp_name) === false) {
alert("上传失败。");
}
if ($file_size >$max_size) {
alert("上传文件大小超过限制。");
}
$dir_name = empty($_GET['dir']) ?'image': trim($_GET['dir']);
if (empty($ext_arr[$dir_name])) {
alert("目录名不正确。");
}
$temp_arr = explode(".",$file_name);
$file_ext = array_pop($temp_arr);
$file_ext = trim($file_ext);
$file_ext = strtolower($file_ext);
if (in_array($file_ext,$ext_arr[$dir_name]) === false) {
alert("上传文件扩展名是不允许的扩展名。\n只允许".implode(",",$ext_arr[$dir_name]) ."格式。");
}
if ($dir_name !== '') {
$save_path .= $dir_name ."/";
$save_url .= $dir_name ."/";
if (!file_exists($save_path)) {
mkdir($save_path);
}
}
$ymd = date("Ymd");
$save_path .= $ymd ."/";
$save_url .= $ymd ."/";
if (!file_exists($save_path)) {
mkdir($save_path);
}
$rand_str = rand(10000,99999);
$new_file_name = date("YmdHis") .'_'.$rand_str .'.'.$file_ext;
$new_file_name_small = date("YmdHis") .'_'.$rand_str .'_small.'.$file_ext;
$file_path = $save_path .$new_file_name;
if (move_uploaded_file($tmp_name,$file_path) === false) {
alert("上传文件失败。");
}
@chmod($file_path,0644);
$file_url = $save_path .$new_file_name;
if(isImage($file_url) == false){
alert("不是真实图片，不允许上传！");
unlink($file_url);
}
require_once INC_DIR.'/img.class.php';
$t = new ThumbHandler();
$t ->setSrcImg($file_url);
$t ->setDstImg($file_url);
$t ->createImg(1000,1000);
header('Content-type: text/html; charset=UTF-8');
$json = new Services_JSON();
echo $json->encode(array('error'=>0,'url'=>$file_url));
exit;
}
function alert($msg) {
header('Content-type: text/html; charset=UTF-8');
echo $msg;
exit;
}

?>