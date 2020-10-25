<?php

if (empty($_GET['reply_id'])) die;

$reply_id = intval($_GET['reply_id']);

$reply = dt_query_one("SELECT topic_id, floor FROM forum_topic_reply WHERE id = $reply_id");
if (!$reply) {
	put_info('帖子不存在了！^_^');
	header('Location:'.$_SERVER['HTTP_REFERER']);
	die;
}

$limit = require_once('inc/topic_show_limit.php');
$page = ceil(dt_count('forum_topic_reply', "WHERE topic_id = ".$reply['topic_id']." AND floor <= ".$reply['floor']) / $limit);

header('Location:'.s_url('?c=forum&a=topic_show&topic_id='.$reply['topic_id'].'&page='.$page).'#r_'.$reply_id);
die;
