<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_GET['reply_id'])) die;

$reply_id = intval($_GET['reply_id']);

$reply = dt_query_one("SELECT topic_id, user_id, del_c, up_c FROM forum_topic_reply WHERE id = $reply_id"); 
if (!$reply) die('获取数据失败!');

require_once('inc/forum_city.php');
require_once('inc/del_padding.php');
global $config;
if ($reply['up_c']+$del_padding > $reply['del_c']
	&& !dt_query_one("SELECT id FROM forum WHERE id = (SELECT forum_id FROM forum_topic WHERE id = ".$reply['topic_id'].") AND user_id = ".$auth['id']." LIMIT 1") 
	&& $reply['user_id'] != $auth['id']
	&& !in_array($auth['id'], $config['manager'])
	&& dt_query_one("SELECT user_id FROM forum_city_info WHERE id = $forum_city")['user_id'] != $auth['id']) {

		if (dt_query_one("SELECT id FROM forum_topic_reply_del WHERE reply_id = $reply_id AND user_id = ".$auth['id']." LIMIT 1")) die('每个人只能删除一次哟！^_^');

		$rs = dt_query("INSERT INTO forum_topic_reply_del (reply_id, user_id, c_at) VALUES ($reply_id, ".$auth['id'].", ".time().")"); 
		if (!$rs) die('赞数据变更失败!');
		$rs = dt_query("UPDATE forum_topic_reply SET del_c = del_c + 1 WHERE id = $reply_id");
		if (!$rs) die('更新赞统计失败！');
		die('s0');
	}

$rs = dt_query("DELETE FROM forum_topic_reply_del WHERE reply_id in (SELECT id FROM forum_topic_reply WHERE id = $reply_id)"); 
if (!$rs) die('回复删除数据变更失败!');
$rs = dt_query("DELETE FROM forum_topic_reply_up WHERE reply_id in (SELECT id FROM forum_topic_reply WHERE id = $reply_id)"); 
if (!$rs) die('回复赞数据变更失败!');
$rs = dt_query("DELETE FROM forum_topic_reply WHERE id = $reply_id"); 
if (!$rs) die('回复数据变更失败!');

die('s1');
