<?php 

if (empty($_GET['check'])) die;

$check = intval($_GET['check']);

switch ($check) {
case 1:
	$username = filter_var($_GET['username'], FILTER_SANITIZE_EMAIL);

	if (empty($username)) die('用户名为空！^_^');
	if (0 < dt_count('user', "WHERE username = '$username'")) die('已经被其他用户注册！^_^');

	die('s0');
	break;
case 2:
	$email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);

	if (empty($email)) die('邮箱为空！^_^');
	if (!is_email($email)) die('邮箱格式不正确！^_^');
	if (0 < dt_count('user', "WHERE email = '$email'")) die('邮箱已经被其他用户注册！^_^');

	die('s0');
	break;
}

