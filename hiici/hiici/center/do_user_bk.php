<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET['user_id'])) die;
$user_id = doubleval($_GET['user_id']);

global $config;
if (!in_array($auth['id'], $config['manager']) || in_array($user_id, $config['manager'])) die('非法的操作！^_^');

$cond = "WHERE user_id = $user_id";

if (dt_query_one("SELECT id FROM user_bk $cond LIMIT 1")) {
	$rs = dt_query("DELETE FROM user_bk $cond");
	if (!$rs) die('删除数据失败！');
} else {
	$rs = dt_query("INSERT INTO user_bk (user_id, c_at) VALUES ($user_id, ".time().")");
	if (!$rs) die('新增数据失败！');
}

header('Location:?c=center&user_id='.$user_id);
