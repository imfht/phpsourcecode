<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET['to_user_id'])) die;

$to_user_id = intval($_GET['to_user_id']);

$cond = "WHERE user_id = ".$auth['id']." AND to_user_id = $to_user_id";

if (!dt_query_one("SELECT id FROM follow $cond LIMIT 1")) {
	$rs = dt_query("INSERT INTO follow (user_id, to_user_id, c_at) VALUES (".$auth['id'].", $to_user_id, ".time().")");
	if (!$rs) die('新增数据失败！');
	$rs = dt_query("UPDATE user_info SET follow_c = follow_c + 1 WHERE id = ".$auth['id']);
	if (!$rs) die('统计数据失败！');
	$rs = dt_query("UPDATE user_info SET fan_c = fan_c + 1 WHERE id = $to_user_id");
	if (!$rs) die('统计数据失败！');
	die('s0');
} else {
	$rs = dt_query("DELETE FROM follow $cond");
	if (!$rs) die('删除数据失败！');
	$rs = dt_query("UPDATE user_info SET follow_c = follow_c - 1 WHERE id = ".$auth['id']);
	if (!$rs) die('统计数据失败！');
	$rs = dt_query("UPDATE user_info SET fan_c = fan_c - 1 WHERE id = $to_user_id");
	if (!$rs) die('统计数据失败！');
	die('s1');
}
