<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

if (!defined('IN_PHPCRAZY')) exit;

$PageTitle = sprintf(L('用户名 用户中心'), $U['username']);

if (!$U['login']) {

	header('Location: '.HomeUrl('index.php/main:login/'));
	AppEnd();
}

$A = new Auth();

$Auth = $A->IsAuth($U['id']);

include T('user');

AppEnd();

?>