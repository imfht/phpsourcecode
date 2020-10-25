<?php
/**
 *  @version 1.0
 *  @dataProvider 2011-01-01 20:19
 */
if(!defined('SITE_VERSION')) {
	define('SITE_VERSION', '0.0.0'); //不带V 固定两点 三段
	define('SITE_RELEASE', '1'); //预留发布编号
	define('SITE_FIXBUG', '2011-01-11'); //版本发布日期
}
$ajax_version = isset($_GET['ajax_version']) && !empty($_GET['ajax_version']) ? $_GET['ajax_version']:'';
if($ajax_version){
	//TODO 增加安全校验
	header("Access-Control-Allow-Origin:*");
	echo '{"version":"'.SITE_VERSION.'","release":"'.SITE_RELEASE.'","fixbug":"'.SITE_FIXBUG.'"}';
}
?>