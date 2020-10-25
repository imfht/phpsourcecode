<?php
/*
*	Package:		PHPCrazy.QQConnect
*	Link:			http://git.oschina.net/Crazy-code/PHPCrazy.QQConnect
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

if (!$GLOBALS['U']['login']) {

	header('Location: ' .HomeUrl());

	AppEnd();
}

$sql = 'UPDATE ' . USERS_TABLE . " 
	SET qq_openid = '' 
	WHERE id = " . (int) $GLOBALS['U']['id'];

$PDO->exec($sql);

header('Location: '.HomeUrl('index.php/main:login/?action=logout'));

AppEnd();