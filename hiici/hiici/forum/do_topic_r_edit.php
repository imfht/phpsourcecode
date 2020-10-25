<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_POST['reply_id'])) die;

$reply_id = intval($_POST['reply_id']);
$content = cleanjs($_POST['content']);

require_once('inc/forum_city.php');
global $config;
if (!dt_query_one("SELECT id FROM forum_topic_reply WHERE id = $reply_id AND user_id = ".$auth['id']." LIMIT 1") 
	&& !dt_query_one("SELECT id FROM forum WHERE id = (SELECT forum_id FROM forum_topic WHERE id = (SELECT topic_id FROM forum_topic_reply WHERE id = $reply_id)) AND user_id = ".$auth['id']." LIMIT 1") 
	&& !in_array($auth['id'], $config['manager'])
	&& dt_query_one("SELECT user_id FROM forum_city_info WHERE id = $forum_city")['user_id'] != $auth['id']) die('没有权限！^_^');

if (empty($content)) die('空的内容！^_^');

$rs = dt_query("UPDATE forum_topic_reply SET content = '$content' WHERE id = $reply_id");
if (!$rs) die('更新reply数据失败！^_^');

die('s0');
