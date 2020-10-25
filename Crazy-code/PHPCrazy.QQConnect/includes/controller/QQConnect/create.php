<?php
/*
*	Package:		PHPCrazy.QQConnect
*	Link:			http://git.oschina.net/Crazy-code/PHPCrazy.QQConnect
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

if ($GLOBALS['U']['login']) {

	header('Location: '.HomeUrl('index.php/main:user/'));

	AppEnd();
}

include Lang('qqconnect');

$QQC = new QQConnect();

$User = $QQC->UserInfo($_SESSION["access_token"], $_SESSION["openid"]);
$username = $User->nickname;

//print_r($_SESSION);exit;


if (isset($_POST['submit'])) {
	
	$username = isset($_POST['username']) ? $_POST['username'] : $username;

	$email = isset($_POST['email']) ? $_POST['email'] : '';

	$password1 = isset($_POST['password1']) ? $_POST['password1'] : '';

	$password2 = isset($_POST['password2']) ? $_POST['password2'] : '';

	$Captcha = isset($_POST['captcha']) ? $_POST['captcha'] : '';

	LoadFunc('user');

	$continue = true;
	$error = array();

	$result = Vusername($username);

	if ($result['error']) {

		$continue = false;
		$error[] = $result['error_msg'];
	}

	$result = Vemail($email);
	
	if ($result['error']) {
		$continue = false;
		$error[] = $result['error_msg'];
	}

	if ( empty($password1) || empty($password2)) {

		$continue = false;
		$error[] = L('密码不能为空');
	}

	if ($password1 != $password2) {
		$continue = false;
		$error[] = L('输入密码不一样');
	}

	if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $email)) {
		$continue = false;
		$error[] = L('邮箱无效');
	}

	// Session_strat() ??
	// new QQConnect() 已进行初始化
	// 看：class.qqconnect.php 的 __construct 函数
	if ($_SESSION['Captcha'] != $Captcha) {
		var_dump($_SESSION['Captcha']);
		$continue = false;
		$error[] = L('验证码不正确');
	}

	if (empty($_SESSION["openid"])) {
		
		$continue = false;
		$error[] = L('openid 为空');
	}

	$sql = 'SELECT qq_openid 
		FROM ' . USERS_TABLE . ' 
		WHERE qq_openid = :qq_openid';

	$result = $PDO->prepare($sql);

	$result->execute(array(':qq_openid' => $_SESSION["openid"]));

	if ($userInfo = $result->fetch(PDO::FETCH_ASSOC)) {

		$continue = false;
		$error[] = L('QQC 已被绑定');
	}


	if ($continue) {
		
		$RegData = RegisterUser($username, $email, $password2);

		// 设置用户的Open ID
		$sql = 'UPDATE ' . USERS_TABLE . ' 
			SET qq_openid = :qq_openid
			WHERE id = :id';

		$result = $PDO->prepare($sql);

		$result->execute(array(
			':qq_openid' 	=> $_SESSION["openid"],
			':id'			=> $RegData[':id'])
		);


		LoadFunc('misc');

		// 发送Email
		$message = UseEmailTpl('registered', array(
			'SITENAME' => $GLOBALS['C']['sitename'],
			'SITEURL' => HomeUrl(),
			'PASSWORD' => $password2,
			'USERNAME' => $username)
		);

		$email = new Email($GLOBALS['C']['system_mail'], array($email), L('注册成功'), $message);

		if (!$email->send()) {

			throw new Exception($email->ErrorInfo);
		}

		$S->Login($RegData[':sid']);

		header('Location: '.HomeUrl('index.php/main:user/'));
		
		AppEnd();
	}
}

$PageTitle = L('QQC 创建新用户');

include T('QQConnect/create');