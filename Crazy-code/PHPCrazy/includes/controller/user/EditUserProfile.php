<?php

if (!defined('IN_PHPCRAZY')) exit;

$PageTitle = '修改资料 - 用户中心';

if (!$GLOBALS['U']['login']) {
	header('Location: '.HomeUrl('index.php/main:login/'));
	AppEnd();
}

$sql = 'SELECT username, email, password
	FROM ' . USERS_TABLE . '
	WHERE id = ' . (int)$GLOBALS['U']['id'];

$result = $PDO->query($sql);

$UserInfo = $result->fetch(PDO::FETCH_ASSOC);


$submit = isset($_POST['submit']) ? true : false;

$continue = true;

$error = array();

$continue_finish = false;

if ($submit) {
	
	LoadFunc('user');

	$username = isset($_POST['username']) ? $_POST['username'] : '';

	$email = isset($_POST['email']) ? $_POST['email'] : '';

	$password = isset($_POST['password']) ? $_POST['password'] : '';

	$password1 = isset($_POST['password1']) ? $_POST['password1'] : '';

	$password2 = isset($_POST['password2']) ? $_POST['password2'] : '';	
	
	$result = Vusername($username, $GLOBALS['U']['username']);

	if ($result['error']) {
		$continue = false;
		$error[] = $result['error_msg'];
	}

	if (empty($password)) {
		
		$error[] = L('密码不能为空');
		$continue = false;
	}

	if (!empty($password1) && ($password1 != $password2)) {
		
		$error[] = L('输入密码不一样');
		$continue = false;
	}

	$result = Vemail($email, $GLOBALS['U']['email']);

	if ($result['error']) {
		$continue = false;
		$error[] = $result['error_msg'];
	}

	if (md5($password) != $UserInfo['password']) {
		$continue = false;
		$error[] = L('密码错误');
	}

	if ($continue) {

		$new_password = (!empty($password2)) ? md5($password2) : $UserInfo['password'];


		$sql = 'UPDATE ' . USERS_TABLE . '
			SET username = :username, email = :email, password = :password 
			WHERE id = :id';
		
		$result = $PDO->prepare($sql);

		$result->execute(array(
			':username' => $username,
			':email' => $email,
			':password' => $new_password,
			':id' => (int)$GLOBALS['U']['id'])
		);
			
		$continue_finish = true;

		$UserInfo['username'] = $username;

		$UserInfo['email'] = $email;

	}

}

include T('user/EditUserProfile');

AppEnd();

?>