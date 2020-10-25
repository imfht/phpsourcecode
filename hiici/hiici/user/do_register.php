<?php 

if (empty($_POST['password'])) die;

//check the agreement
if (empty($_POST['agreement'])) { put_info('请确认同意注册协议！^_^'); header('Location:?c=user&a=register'); die; }

$username = filter_var($_POST['username'], FILTER_SANITIZE_EMAIL);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];
$r_password = $_POST['r_password'];
$email_code = $_POST['email_code'];

//check the email_code
if ($email_code != $_SESSION['email_code'] || $email != $_SESSION['email']) { put_info('邮箱激活码不正确！^_^'); header('Location:?c=user&a=register'); die; }

if (empty($username) || empty($email) || empty($password)) { put_info('用户名 或 密码 为空！'); header('Location:?c=user&a=register'); die; }

if (0 < dt_count('user', "WHERE username = '$username'")) { put_info('用户名已经被其他用户注册！^_^'); header('Location:?c=user&a=register'); die; }

if ($password != $r_password) { put_info('两次输入的密码不一致！'); header('Location:?c=user&a=register'); die; }

$rs = dt_query("INSERT INTO user (username, password, email, c_at) VALUES ('$username', '".sha1($password)."', '$email', ".time().")");
if (!$rs) { put_info('注册失败！'); header('Location:?c=user&a=register'); die; }

put_info('注册成功了！'.$username);
header('Location:?c=user&a=login');
die;

