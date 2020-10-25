<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
session_start();
error_reporting(E_ALL ^ E_NOTICE);
define('FileLink','..');
@include FileLink.'/Public/Resources/Config.php';
include 'Action/Root_Hackdone_Action.php';
$Mark_Url_Action = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
?> 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title><?php echo $Mark_Config_Action['site_name']; ?></title>
<link rel="stylesheet" type="text/css" href="<?php __ROOT__('Css/style.css');?>" />
<link rel="stylesheet" type="text/css" href="<?php __ROOT__('Css/bootstrap.min.css');?>" />
</head>
<body>
<div id="Wrapper">
<header id="header">
  <hgroup id="title">
  	<!--<div id="logo"></div>-->
    <h1><span><?php echo $Mark_Config_Action['site_name']; ?></span></h1>
    <h2><?php echo $Mark_Config_Action['nametwo']; ?></h2>
  </hgroup>
  <nav class="navigation">
		<a href="<?php echo $Mark_Config_Action['site_link'].$Mark_Config_Action['level']; ?>" target="_blank">返回前台</a>
				<a href="index.php">后台首页</a>
				<a href="Post.php">查看日志</a>
				<a href="Page.php"> 查看页面</a>
				<a href="Links.php"> 友情链接</a>
				<a href="Config.php">系统设置</a>
				<a href="Head.php?getout=logout"> 退出系统</a>
		  </nav> 
</header>