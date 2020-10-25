<?php 

if (empty($_POST['password'])) die;

//check the captcha_code 
if (!empty($_SESSION['securimage'])) {
	require_once('inc/securimage/securimage.php');
	$securimage = new Securimage();
	if (!$securimage->check(filter_var($_POST['captcha_code'], FILTER_SANITIZE_EMAIL))) {
		clean_remember_password();
		put_info('验证码错误！^_^');
		header('Location:?c=user&a=login');
		die();
	}			
}

$username = filter_var($_POST['username'], FILTER_SANITIZE_EMAIL);
$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

//记住密码
setcookie('user_username', $username, time()+3600*24*30, '/');
if (!empty($_POST['remember_password'])) {
	setcookie('user_password', $password, time()+3600*24*30, '/');
}

if (empty($username) || empty($password)) {
	put_info('用户名 或 密码 为空！^_^');
	header('Location:?c=user&a=register');
	die();
}

$user = dt_query_one("SELECT id, username FROM user WHERE ((username = '$username' AND password = '".$password."') OR (email = '$username' AND password = '".$password."')) AND email is not null");
if (!$user) {
	put_info('登录失败！^_^');
	$_SESSION['securimage'] = 'Y';
	header('Location:?c=user&a=login');
	die();
}

require_once('inc/login_pub_inc.php');
die();
