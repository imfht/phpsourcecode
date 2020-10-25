<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

global $config;
if (!in_array($auth['id'], $config['manager'])) die('用户权限不够!');

if (empty($_POST['kind'])) die;

$kind = filter_var($_POST['kind'], FILTER_SANITIZE_STRING);
$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$user_id = intval($_POST['user_id']);

if (empty($name) || empty($kind)) {
	put_info('表单内容不规范！');
	header('Location:?c=finance&a=lmcy_add');
	die;
}


if (0 < dt_count('finance_lmcy', 'WHERE user_id ='.$user_id)) {
	put_info('重复的用户ID！^_^');
	header('Location:?c=finance&a=lmcy_add');
	die;
}

$rs = dt_query("INSERT INTO finance_lmcy (name, user_id, kind, c_at) 
	VALUES ('$name', '$user_id', '$kind', ".time().")");
if (!$rs) {
	put_info('成员添加失败！');
	header('Location:?c=finance&a=lmcy_add');
	die;
}

// 清空cookie
foreach (array_keys($_POST) as $n) {
	setcookie('lmcy_add_'.$n, '', time()-3600, '/');
}

put_info('成员添加成功！');
header('Location:?c=finance&a=lmcy_manage');
die;

