<?php 

if (dt_query_one("SELECT id FROM user_bk WHERE user_id = ".$user['id']." LIMIT 1")) { put_info('您被加入黑名单了，快联系工作人员洗白吧！^_^'); header('Location:?c=user&a=login'); die(); }

$user_info = dt_query_one("SELECT name FROM user_info WHERE id = ".$user['id']);
if (!$user_info) {
	//初始化user_info
	$user_name = '用户'.$user['id'];
	$rs = dt_query("INSERT INTO user_info (id, name, c_at) VALUES ('".$user['id']."', '$user_name', ".time().")");
	if (!$rs) die('新建user_info数据失败！^_^');
} else {
	$user_name = $user_info['name'];
}

$_SESSION['auth'] = array(
	'id' => $user['id'],
	'name' => $user_name);

$_SESSION['securimage'] = null;

//如果是首次登录,跳去初始化account
if (!$user_info) { header('Location:?c=account&a=index'); die(); }

//如果cookie里保存了login_jump,跳转过去
if (!empty($_COOKIE['login_jump'])) {
	//Get the cookie
	$login_jump = filter_var($_COOKIE['login_jump'], FILTER_SANITIZE_URL);
	setcookie('login_jump', '', time()-3600, '/');
	header('Location:'.$login_jump);
	die();
}

header('Location:?c=center');
