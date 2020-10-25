<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_GET['topic_id'])) die;

$topic_id = intval($_GET['topic_id']);

$topic = dt_query_one("SELECT forum_id, pay, up_c, del_c FROM forum_topic WHERE id = $topic_id"); 
if (!$topic) die('获取数据失败!');

if (0 < $topic['pay']) {
	global $config;
	if (!dt_query_one("SELECT id FROM forum_topic WHERE id = $topic_id AND user_id = ".$auth['id']." lIMIT 1") 
		&& !in_array($auth['id'], $config['manager'])) topic_del_c($topic_id, $auth);
} else {
	require_once('inc/forum_city.php');
	require_once('inc/del_padding.php');
	global $config;
	if ($topic['up_c']+$del_padding > $topic['del_c']
		&& !dt_query_one("SELECT id FROM forum_topic WHERE id = $topic_id AND user_id = ".$auth['id']." lIMIT 1") 
		&& !dt_query_one("SELECT id FROM forum WHERE id = ".$topic['forum_id']." AND user_id = ".$auth['id']." LIMIT 1") 
		&& !in_array($auth['id'], $config['manager'])
		&& dt_query_one("SELECT user_id FROM forum_city_info WHERE id = $forum_city")['user_id'] != $auth['id']) topic_del_c($topic_id, $auth);
}

//是否存在扩展
$forum_ext = dt_query_one("SELECT ext FROM forum WHERE id = ".$topic['forum_id'])['ext'];
if (0 != $forum_ext) {
	$rs = dt_query("DELETE FROM forum_topic_ext_$forum_ext WHERE id = $topic_id"); 
	if (!$rs) die('forum_topic_ext数据变更失败!^_^');
} 
$rs = dt_query("DELETE FROM forum_topic WHERE id = $topic_id"); 
if (!$rs) die('forum_topic数据变更失败!^_^');
$rs = dt_query("DELETE FROM forum_topic_del WHERE topic_id = $topic_id"); 
if (!$rs) die('回复删除数据变更失败!');
$rs = dt_query("DELETE FROM forum_topic_up WHERE topic_id = $topic_id"); 
if (!$rs) die('forum_topic_up数据变更失败!^_^');
$rs = dt_query("DELETE FROM forum_topic_reply_up WHERE reply_id in (SELECT id FROM forum_topic_reply WHERE topic_id = $topic_id)"); 
if (!$rs) die('forum_topic_reply_up数据变更失败!^_^');
$rs = dt_query("DELETE FROM forum_topic_reply WHERE topic_id = $topic_id"); 
if (!$rs) die('forum_topic_reply数据变更失败!^_^');
$rs = dt_query("DELETE FROM forum_topic_order WHERE topic_id = $topic_id"); 
if (!$rs) die('forum_topic_order数据变更失败!^_^');

$rs = dt_query("UPDATE forum SET topic_c = topic_c - 1, reply_c = reply_c - 1 WHERE id = ".$topic['forum_id']);
if (!$rs) die('更新forum数据失败！');

die('s1');

function topic_del_c($topic_id, $auth) {
	if (dt_query_one("SELECT id FROM forum_topic_del WHERE topic_id = $topic_id AND user_id = ".$auth['id']." LIMIT 1")) die('每个人只能删除一次哟！^_^');

	$rs = dt_query("INSERT INTO forum_topic_del (topic_id, user_id, c_at) VALUES ($topic_id, ".$auth['id'].", ".time().")"); 
	if (!$rs) die('赞数据变更失败!');
	$rs = dt_query("UPDATE forum_topic SET del_c = del_c + 1 WHERE id = $topic_id");
	if (!$rs) die('更新赞统计失败！');
	die('s0');
}
