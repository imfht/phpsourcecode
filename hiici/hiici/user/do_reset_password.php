<?php

if (empty($_POST['password_new'])) die();

if ($_SESSION['r_p_token'] != $_POST['r_p_token']) die('令牌错误！^_^');

$username = $_SESSION['r_p_username'];
$password_new = $_POST['password_new'];
$password_new_r = $_POST['password_new_r'];

if (!dt_query_one("SELECT id FROM user WHERE username = '$username' OR email = '$username'")) {
	put_info('用户不存在！^_^');
	header('Location:?c=user&a=reset_password&r_p_token='.$_SESSION['r_p_token']);
	die;
}

if ($password_new != $password_new_r) {
	put_info('新密码不一致！^_^');
	header('Location:?c=user&a=reset_password&r_p_token='.$_SESSION['r_p_token']);
	die;
}

$rs = dt_query("UPDATE user SET password = '".sha1($password_new)."' WHERE username = '$username' OR email = '$username'" );
if (!$rs) die('数据变更失败！');

$cond = "WHERE username = '$username' OR email = '$username'";
//登录密码
$rs = dt_query("UPDATE user SET password = '".sha1($password_new)."' $cond" );
if (!$rs) die('user数据变更失败！');
//支付密码
$rs = dt_query("UPDATE account SET password = '".sha1($password_new)."' WHERE id = (SELECT id FROM user $cond)");
if (!$rs) die('account数据变更失败！');


$_SESSION['r_p_token'] = null; 
put_info('密码重置成功了！^_^');
header('Location:?c=user&a=login');
die;
