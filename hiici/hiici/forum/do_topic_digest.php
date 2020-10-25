<?php 

$auth = @$_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_GET['topic_id'])) die;

$topic_id = intval($_GET['topic_id']);

$topic = dt_query_one("SELECT forum_id, pay FROM forum_topic WHERE id = $topic_id"); 
if (!$topic) die('获取数据失败!');

if (0 < $topic['pay']) {
	global $config;
	if (!in_array($auth['id'], $config['manager'])) die('没有权限！^_^');
} else {
	require_once('inc/forum_city.php');
	global $config;
	if (1 > dt_count('forum', "WHERE id = ".$topic['forum_id']." AND user_id = ".$auth['id']) 
		&& !in_array($auth['id'], $config['manager'])
		&& dt_query_one("SELECT user_id FROM forum_city_info WHERE id = $forum_city")['user_id'] != $auth['id']) die('没有权限！^_^');
}

if (0 < dt_count('forum_topic', "WHERE id = $topic_id AND digest = 1")) {
	$rs = dt_query("UPDATE forum_topic SET digest = 0 WHERE id = $topic_id");
} else {
	$rs = dt_query("UPDATE forum_topic SET digest = 1 WHERE id = $topic_id");
}
if (!$rs) die('更新赞统计失败！');

die('s0');
