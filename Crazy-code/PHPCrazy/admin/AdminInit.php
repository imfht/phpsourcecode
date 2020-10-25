<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

/**
 * 后台管理页面初始化文件
 */

if (!defined('IN_PHPCRAZY')) exit;

define('IN_ADMIN', true);

// 加载后台页面的函数
LoadFunc('admin');

// 加载后台需要用到的语言包
include Lang('admin');

// 实例化权限
$A = new Auth();

// 取得用户权限
$Auth = $A->IsAuth($U['id']);

// 如果用户没有 管理员 权限则跳转到登录页面
if (!$Auth[ADMIN]) {

	header('Location: '.HomeUrl('index.php/main:login/'));

	AppEnd();
}

?>