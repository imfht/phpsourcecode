<?php

session_start();
$auth = @$_SESSION['auth'];
$umeditor_img_up = @$_SESSION['umeditor_img_up'];
if (empty($auth)) die('用户未登录！^_^');
if (!empty($umeditor_img_up) && time()-2 < $umeditor_img_up) die('上传的过于频繁！^_^');

header("Content-Type:text/html;charset=utf-8");
error_reporting( E_ERROR | E_WARNING );
date_default_timezone_set("Asia/chongqing");
include "Uploader.class.php";
//上传配置
$config = array(
	"savePath" => "upload/" ,             //存储文件夹
	"maxSize" => 40000 ,                   //允许的文件最大尺寸，单位KB
	"allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" )  //允许的文件格式
);
//上传文件目录
$Path = "upload/";

//背景保存在临时目录中
$config[ "savePath" ] = $Path;
$up = new Uploader( "upfile" , $config );
$type = $_REQUEST['type'];
$callback=$_GET['callback'];

$info = $up->getFileInfo();

//if (!in_array($auth['id'], $config['manager'])) {
if (!preg_match('/top_ads_content$/', @$_SERVER['QUERY_STRING'])) {
	//加水印
	require_once('../../lib/water_mark.php');
	require_once('../../../forum/inc/forum_city.php');
	water_mark($info['url'], '../../../img/forum/forum_logo/'.$forum_city.'.png');
	$_SESSION['umeditor_img_up'] = time();
}

require_once('../../config.php');
if ($config['OSS_ACCESS_ID']) require_once('oss.php');  //如果开启了阿里OSS服务

/**
 * 返回数据
 */
if($callback) {
	echo '<script>'.$callback.'('.json_encode($info).')</script>';
} else {
	echo json_encode($info);
}
