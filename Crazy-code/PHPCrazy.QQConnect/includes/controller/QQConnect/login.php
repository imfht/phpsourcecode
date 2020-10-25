<?php
/*
*	Package:		PHPCrazy.QQConnect
*	Link:			http://git.oschina.net/Crazy-code/PHPCrazy.QQConnect
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

if ($GLOBALS['U']['login']) {
	
	header('location: '.HomeUrl());

} else {

	include Lang('qqconnect');

	if (isset($_GET['code']) && isset($_GET['state'])) {

		$QQC = new QQConnect();

		$QQC->AccessToken($_GET['code']);

		$QQC->Openid();

		if (!isset($_SESSION['openid']) || empty($_SESSION['openid'])) {

			throw new Exception(L('无法获取openid'));	
		}

		$sql = 'SELECT sid
			FROM ' . USERS_TABLE . '
			WHERE qq_openid = :qq_openid';

		$result = $PDO->prepare($sql);

		$result->execute(array(':qq_openid' => $_SESSION['openid']));

		if ($userInfo = $result->fetch(PDO::FETCH_ASSOC)) {

			$S->Login($userInfo['sid']);

			header('Location: '.HomeUrl('index.php/main:user/'));
			
			AppEnd();

		} else {
			
			// 引导用户绑定、激活账号
			include T('QQConnect/Welcome');

			AppEnd();
		}

	} else {

		Message(WARNING, L('错误'), L('非法操作'));
	
	}
}