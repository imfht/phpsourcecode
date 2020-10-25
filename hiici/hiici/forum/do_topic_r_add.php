<?php

$auth = @$_SESSION['auth'];
if (empty($auth)) die(json_encode(array('msg' => '用户未登录！^_^', 'token' => get_token())));

if (empty($_POST)) die;

$topic_id = @intval($_POST['topic_id']);
$content = cleanjs($_POST['content']);

if (empty($content)) die(json_encode(array('msg' => '空的内容！^_^', 'token' => get_token())));
if (time()-300 < dt_query_one("SELECT c_at FROM forum_topic_reply WHERE user_id = ".$auth['id']." ORDER BY c_at DESC LIMIT 1")['c_at']) die(json_encode(array('msg' => '回复的过于频繁！^_^', 'token' => get_token())));

if (empty($topic_id)) {
	$reply_id = intval($_POST['reply_id']);
	$reply = dt_query_one("SELECT topic_id, user_id, floor FROM forum_topic_reply WHERE id = $reply_id");
	if (!$reply) die('获取reply数据失败！');
	$topic_id = $reply['topic_id'];
	$content = '<a class="to-topic-show" href="?c=forum&a=to_topic_show_reply&reply_id='.$reply_id.'"><span class="glyphicon glyphicon-share-alt"></span> 回复 '.$reply['floor'].'楼</a><br><br>'.$content;
	$rs = dt_query("UPDATE forum_topic_reply SET reply_c = reply_c + 1 WHERE id = $reply_id");
	if (!$rs) die('更新数据失败！');
}

$reply_l = dt_query_one("SELECT floor FROM forum_topic_reply WHERE topic_id = $topic_id ORDER BY floor DESC LIMIT 1");
$floor = ($reply_l) ? $reply_l['floor'] + 1 : 1;

$rs = dt_query("INSERT INTO forum_topic_reply (topic_id, content, user_id, user_name, floor, c_at) 
	VALUES ('$topic_id', '$content', ".$auth['id'].", '".$auth['name']."', $floor, ".time().")");
if (!$rs) die('新建forum_topic_reply数据失败！^_^');

$topic = dt_query_one("SELECT forum_id, title, user_id, user_name FROM forum_topic WHERE id = $topic_id");
if (!$topic) die('获取topic数据失败！');

//添加系统信息
$new_reply_id = dt_query_one("SELECT LAST_INSERT_ID()")[0];
if (empty($reply)) {
	if (!msg_sys_add('[<b>'.$auth['name'].'</b>] 为您的话题贴 <a href="?c=forum&a=to_topic_show_reply&reply_id='.$new_reply_id.'">'.get_substr($topic['title']).'</a> 添加了新的回复', $topic['user_id'])) die('系统信息发送失败！^_^');
} else {
	if (!msg_sys_add('[<b>'.$auth['name'].'</b>] 在 [<b>'.$topic['user_name'].'</b>] 的话题贴 <a href="?c=forum&a=to_topic_show_reply&reply_id='.$new_reply_id.'">'.get_substr($topic['title']).'</a> 里为您的回复添加了新的回复', $reply['user_id'])) die('系统信息发送失败！^_^');
}

//更新topic统计数据
$rs = dt_query("UPDATE forum_topic SET reply_c = reply_c + 1, l_r_user_id = ".$auth['id'].", l_r_user_name = '".$auth['name']."', l_r_at = ".time()." WHERE id = $topic_id");
if (!$rs) die('更新topic数据失败！^_^');

//更新forum统计数据
$rs = dt_query("UPDATE forum SET reply_c = reply_c + 1 WHERE id = ".$topic['forum_id']);
if (!$rs) die('更新forum数据失败！^_^');

if (0 < dt_count('forum', "WHERE id = ".$topic['forum_id']." AND today = ".date('Ymd', time()))) {
	$rs = dt_query("UPDATE forum SET today_reply_c = today_reply_c + 1 WHERE id = ".$topic['forum_id']);
} else {
	$rs = dt_query("UPDATE forum SET today = ".date('Ymd', time()).", today_reply_c = 1, today_up_c = 0 WHERE id = ".$topic['forum_id']);
}
if (!$rs) die('更新today_reply_c数据失败！^_^');

die('s0');
