<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET['forum_id'])) die;

$forum_id = intval($_GET['forum_id']);

global $config;
if (!in_array($auth['id'], $config['manager'])) die('没有权限！^_^');

$topics = dt_query("SELECT id, forum_id FROM forum_topic WHERE forum_id = $forum_id");
if (!$topics) die('获取topics数据失败!');

while($topic = mysql_fetch_array($topics)) {
	$forum_ext = dt_query_one("SELECT ext FROM forum WHERE id = ".$topic['forum_id'])['ext'];
	if (0 != $forum_ext) {
		$rs = dt_query("DELETE FROM forum_topic_ext_$forum_ext WHERE id = ".$topic['id']); 
		if (!$rs) die('forum_topic_ext数据变更失败!^_^');
	} 
	$rs = dt_query("DELETE FROM forum_topic WHERE id = ".$topic['id']); 
	if (!$rs) die('forum_topic数据变更失败!^_^');
	$rs = dt_query("DELETE FROM forum_topic_up WHERE topic_id = ".$topic['id']); 
	if (!$rs) die('forum_topic_up数据变更失败!^_^');
	$rs = dt_query("DELETE FROM forum_topic_reply_up WHERE reply_id in (SELECT id FROM forum_topic_reply WHERE topic_id = ".$topic['id'].")"); 
	if (!$rs) die('forum_topic_reply_up数据变更失败!^_^');
	$rs = dt_query("DELETE FROM forum_topic_reply WHERE topic_id = ".$topic['id']); 
	if (!$rs) die('forum_topic_reply数据变更失败!^_^');
	$rs = dt_query("DELETE FROM forum_topic_order WHERE topic_id = ".$topic['id']); 
	if (!$rs) die('forum_topic_order数据变更失败!^_^');
}
$rs = dt_query("DELETE FROM forum_follow WHERE forum_id = $forum_id"); 
if (!$rs) die('数据变更失败!');
$rs = dt_query("DELETE FROM forum WHERE id = $forum_id"); 
if (!$rs) die('forum数据变更失败!');

put_info('成功删除！^_^');
header('Location:?c=forum');
die;
