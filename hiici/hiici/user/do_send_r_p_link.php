<?php 

if (empty($_POST)) die;

//check the captcha_code 
require_once 'inc/securimage/securimage.php';
$securimage = new Securimage();
if (!$securimage->check(filter_var($_POST['captcha_code'], FILTER_SANITIZE_EMAIL))) { 
	put_info('验证码错误！'); 
	header('Location:?c=user&a=send_r_p_link'); 
	die; 
}			

$username = filter_var($_POST['username'], FILTER_SANITIZE_EMAIL);

if (empty($username)) {
	put_info('用户名为空！');
	header('Location:?c=user&a=send_r_p_link');
	die;
}

$user = dt_query_one("SELECT email FROM user WHERE username = '$username' OR email = '$username'");
if (!$user) {
	put_info('用户不存在！^_^');
	header('Location:?c=user&a=send_r_p_link');
	die;
}

$_SESSION['r_p_token'] = sha1(rand());
$_SESSION['r_p_username'] = $username;
$r_p_link = 'http://'.$_SERVER['HTTP_HOST'].'?c=user&a=reset_password&r_p_token='.$_SESSION['r_p_token'];

if (!send_email($user['email'], '【'.SYS_NAME.'】密码重置链接。', $r_p_link)) {
	put_info('发送失败！^_^');
	header('Location:?c=user&a=send_r_p_link');
	die;
}

put_info('密码重置链接已经发送到您的邮箱，您可以通过通过该链接重置密码了。^_^');
header('Location:?c=user&a=login');
die;
