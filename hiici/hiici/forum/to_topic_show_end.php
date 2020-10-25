<?php

if (empty($_GET['topic_id'])) die;

$topic_id = intval($_GET['topic_id']);

$reply = dt_query_one("SELECT id FROM forum_topic_reply WHERE topic_id = $topic_id ORDER BY id DESC LIMIT 1");
if (!$reply) {
	header('Location:'.s_url('?c=forum&a=topic_show&topic_id='.$topic_id));
	die;
}

header('Location:?c=forum&a=to_topic_show_reply&reply_id='.$reply['id']);
die;
