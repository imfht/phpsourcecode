<?php
/*
*	PHPCrazy 后台管理入口文件
*	
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

define('IN_PHPCRAZY', true);

require_once 'PHPCrazy.php';

$A = new Auth();

$Auth = $A->IsAuth($U['id']);

if ($Auth[ADMIN]) {

	$admin_url = ADMIN_PATH . '/' . ADMIN_FILE;

	header("Location: ".HomeUrl($admin_url));

	AppEnd();

}

header('Location: '.HomeUrl());

AppEnd();

?>