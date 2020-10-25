<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET['shuo_id'])) die;

$shuo_id = intval($_GET['shuo_id']);

$cond = "WHERE id = $shuo_id AND user_id = ".$auth['id'];

if (1 > dt_count('shuo', $cond)) die('违规删除非属说说！');

$rs = dt_query("DELETE FROM shuo $cond"); 
if (!$rs) die('说说数据变更失败!');
$rs = dt_query("DELETE FROM shuo_up WHERE shuo_id = $shuo_id"); 
if (!$rs) die('说说赞数据变更失败!');
$rs = dt_query("DELETE FROM shuo_reply_up WHERE shuo_reply_id in (SELECT id FROM shuo_reply WHERE shuo_id = $shuo_id)"); 
if (!$rs) die('说说回复赞数据变更失败!');
$rs = dt_query("DELETE FROM shuo_reply WHERE shuo_id = $shuo_id"); 
if (!$rs) die('说说回复数据变更失败!');

$rs = dt_query("UPDATE user_info SET shuo_c = shuo_c - 1 WHERE id = ".$auth['id']);
if (!$rs) die('更新说说统计失败！');

die('s0');
