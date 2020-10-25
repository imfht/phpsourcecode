<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

if (!defined('IN_PHPCRAZY')) exit;

$action = isset($_GET['action']) ? $_GET['action'] : '';

// 注册
if ($action == 'register') {

	// 如果已登录则跳转到用户中心
	if ($GLOBALS['U']['login']) {
		header('Location: index.php/main:user/');
		AppEnd();
	}
	
	$submit = isset($_POST['submit']) ? true : false;

	$username = isset($_POST['username']) ? $_POST['username'] : '';

	$password1 = isset($_POST['password1']) ? $_POST['password1'] : '';

	$password2 = isset($_POST['password2']) ? $_POST['password2'] : '';

	$email = isset($_POST['email']) ? $_POST['email'] : '';

	$Captcha = isset($_POST['captcha']) ? $_POST['captcha'] : '';

	if ($submit) {
		
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

		if ( ($password1 == '') || ($password2 == '') ) {
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

		if(!isset($_SESSION)) {
		  
		    session_start();
		}

		if ($_SESSION['register_captcha'] != $Captcha) {
			
			$continue = false;
			$error[] = L('验证码不正确');
		}		

		// 为了和HTML分离, 目前只想到这方法
		if ($continue) {
				
			$RegData = RegisterUser($username, $email, $password2);

			LoadFunc('misc');

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

	$GLOBALS['PageTitle'] = L('注册');

	include T('register');

	AppEnd();

// 退出登录
} elseif ($action == 'logout') {

	// 如果用户没有登录直接退出
	if (!$GLOBALS['U']['login']) {
		header('Location: ' . HomeUrl('index.php/main:home/'));
		AppEnd();
	}

	$S->Logout();

	header('Location: ' . HomeUrl('index.php/main:home/'));

	AppEnd();

// 忘记密码
} elseif ($action == 'forgetpassword') {

	$submit = isset($_POST['submit']) ? true : false;

	$finish = false;
	$continue = true;
	$error = array();

	$email = isset($_POST['email']) ? $_POST['email'] : '';

	if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $email)) {
		$continue = false;
		$error[] = L('邮箱无效');
	}

	if ($submit) {
		
		if ($continue) {

			$sql = 'SELECT id, username, activation_key 
				FROM ' . USERS_TABLE . '
				WHERE email = :email';

			$result = $PDO->prepare($sql);

			$result->execute(array(':email' => $email));

			if($row = $result->fetch(PDO::FETCH_ASSOC)) {
				
				LoadFunc('user');
				LoadFunc('misc');
				
				$activation_key = mksid($row['id'], $row['username'], RandString());

				$sql = 'UPDATE ' . USERS_TABLE . ' 
					SET activation_key = :activation_key 
					WHERE id = :id';

				$result = $PDO->prepare($sql);

				$result->execute(array(
					':activation_key' => $activation_key,
					':id' => (int)$row['id'])
				);

				$message = UseEmailTpl('forgetpassword', array(
					'SITENAME' => $GLOBALS['C']['sitename'],
					'SITEURL' => HomeUrl(),
					'URL' => HomeUrl('index.php/main:login/?action=resetpassword&key='.$activation_key),
					'USERNAME' => $row['username'])
				);

				$em = new Email($GLOBALS['C']['system_mail'], array($email), L('找回密码'), $message);

				if (!$em->send()) {

					throw new Exception($em->ErrorInfo);
					
				}

				$finish = true;

			} else {

				$continue = false;
				$error[] = L('邮箱未注册');
			}
		}
	}

	$GLOBALS['PageTitle'] = L('忘记密码');

	include T('forgetpassword');

	AppEnd();

// 重置密码
} elseif ($action == 'resetpassword') {

	$submit = isset($_POST['submit']) ? true : false;

	$activation_key = isset($_GET['key']) ? $_GET['key'] : '';

	if (empty($activation_key)) {

		Message(WARNING, L('错误'), L('非法操作'));

	}

	$sql = 'SELECT id, username, email
		FROM ' . USERS_TABLE . '
		WHERE activation_key = :activation_key';

	$result = $PDO->prepare($sql);

	$result->execute(array(':activation_key' => $activation_key));

	if(!$row = $result->fetch(PDO::FETCH_ASSOC)) {

		Message(WARNING, L('错误'), L('激活链接无效'));
		
	}

	$continue = true;
	$error = array();

	$password1 = isset($_POST['password1']) ? $_POST['password1'] : '';

	$password2 = isset($_POST['password2']) ? $_POST['password2'] : '';

	$username = $row['username'];

	if ($submit) {
		
		if (empty($password1) || empty($password2)) {
			$continue = false;
			$error[] = L('密码不能为空');
		}

		if ($password1 != $password2) {
			$continue = false;
			$error[] = L('输入密码不一样');
		}

		if ($continue) {

			LoadFunc('misc');

			$message = UseEmailTpl('sendpassword', array(
				'SITENAME' => $GLOBALS['C']['sitename'],
				'SITEURL' => HomeUrl(),
				'NEWPASSWORD' => $password2,
				'USERNAME' => $row['username'])
			);

			$email = new Email($GLOBALS['C']['system_mail'], array($row['email']), L('您的新密码'), $message);

			if (!$email->send()) {

				throw new Exception($email->ErrorInfo);

			}

			$sql = 'UPDATE ' . USERS_TABLE . "
				SET activation_key = '', password = '" . md5($password2) . "'
				WHERE id = " . (int)$row['id'];
			
			$PDO->exec($sql);

			Message(SUCCESS, L('提示'), L('密码重置成功'));
		}

	}

	$GLOBALS['PageTitle'] = L('重置密码');

	include T('resetpassword');

	AppEnd();

// 登录
} else {

	// 如果已登录则跳转到用户中心
	if ($GLOBALS['U']['login']) {
		header('Location: ' .HomeUrl('index.php/main:user/'));
		AppEnd();
	}

	$submit = isset($_POST['submit']) ? true : false;
	$account = isset($_POST['account']) ? $_POST['account'] : '';
	$password = isset($_POST['password']) ? $_POST['password'] : '';
	$location = isset($_GET['location']) ? $_GET['location'] : '';

	$form_action = empty($location) ? HomeUrl('index.php/main:login/') : HomeUrl('index.php/main:login/?location='.$location);

	$continue = true;
	$error = array();

	if ($submit) {
	
		if ($password == '') {
			$continue = false;
			$error[] = L('密码不能为空');
		}

		if (empty($account)) {
			$continue = false;
			$error[] = L('账号不能为空');
		}
		
		$password = md5($password);
		
		if ($continue) {

			// 使用 Email 登录
			if (preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $account)) {
				$sql = 'SELECT sid, password
					FROM ' . USERS_TABLE . '
					WHERE email = :account';

			//使用ID登录
			} elseif (preg_match('/^[1-9][0-9]{0,9}$/', $account)) {
				$sql = 'SELECT sid, password
					FROM ' . USERS_TABLE . '
					WHERE id = :account';
			} else {
				$sql = 'SELECT sid, password
					FROM ' . USERS_TABLE . '
					WHERE username = :account';
			}

				
			$result = $PDO->prepare($sql);

			$result->execute(array(':account' => $account));

			if ($userInfo = $result->fetch(PDO::FETCH_ASSOC)) {
			
				if ($userInfo['password'] == $password) {

					$S->Login($userInfo['sid']);

					if (empty($location)) {

						header('Location: '.HomeUrl('index.php/main:user/'));
					} else {

						header('Location: '.HomeUrl(urldecode($location)));
					}
					
					AppEnd();

				} else {

					$continue = false;
					$error[] = L('密码错误');
				}

			} else {
				
				$continue = false;
				$error[] = L('账号不存在');
			}
		}
	}
	
	$GLOBALS['PageTitle'] = L('登录');

	include T('login');

	AppEnd();

}

?>