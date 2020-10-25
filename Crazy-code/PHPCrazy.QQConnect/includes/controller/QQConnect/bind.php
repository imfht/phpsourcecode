<?php
/*
*	Package:		PHPCrazy.QQConnect
*	Link:			http://git.oschina.net/Crazy-code/PHPCrazy.QQConnect
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

$QQC = new QQConnect();

// 如果用户已登录则先注销登陆
if ($GLOBALS['U']['login']) {
	
	$S->Logout();

	header('Location: ' . $QQC->Login());

	AppEnd();
}

$continue = true;
$error = array();

if (isset($_POST['submit'])) {

	$account = isset($_POST['account']) ? $_POST['account'] : '';
	$password = isset($_POST['password']) ? $_POST['password'] : '';

	if ($password == '') {
		$continue = false;
		$error[] = L('密码不能为空');
	}

	if (empty($account)) {
		$continue = false;
		$error[] = L('账号不能为空');
	}

	$password = md5($password);

	// 使用 Email 绑定
	if (preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $account)) {
		
		$sql = 'SELECT id, sid, password, qq_openid
			FROM ' . USERS_TABLE . '
			WHERE email = :account';

	//使用ID绑定
	} elseif (preg_match('/^[1-9][0-9]{4,9}$/', $account)) {

		$sql = 'SELECT id, sid, password, qq_openid
			FROM ' . USERS_TABLE . '
			WHERE id = :account';

	// 用户名绑定
	} else {

		$sql = 'SELECT id, sid, password, qq_openid
			FROM ' . USERS_TABLE . '
			WHERE username = :account';
	}

	$result = $PDO->prepare($sql);

	$result->execute(array(':account' => $account));

	if ($userInfo = $result->fetch(PDO::FETCH_ASSOC)) {
		
		if ($userInfo['password'] == $password) {

			if (empty($userInfo['qq_openid'])) {

				$sql = 'SELECT qq_openid 
					FROM ' . USERS_TABLE . ' 
					WHERE qq_openid = :qq_openid';

				$result = $PDO->prepare($sql);

				$result->execute(array(':qq_openid' => $_SESSION['openid']));

				if ($result->fetch(PDO::FETCH_ASSOC)) {
					
					$continue = false;
					$error[] = L('QQC 已被绑定');
				}

			} else {
				
				$continue = false;
				$error[] = L('QQC 您已绑定');

			}

		} else {

			$continue = false;
			$error[] = L('密码错误');
		}

	} else {
		
		$continue = false;
		$error[] = L('账号不存在');
	}

	if ($continue) {
		
		$sql = 'UPDATE ' . USERS_TABLE . " 
			SET qq_openid = :qq_openid 
			WHERE id = " . (int) $userInfo['id'];

		$result = $PDO->prepare($sql);

		$result->execute(array(':qq_openid' => $_SESSION['openid']));

		$S->Login($userInfo['sid']);

		header('Location: '.HomeUrl('index.php/main:user/'));
		
		AppEnd();
	}
}

$PageTitle = L('QQC 绑定用户');

include T('QQConnect/bind');

AppEnd();