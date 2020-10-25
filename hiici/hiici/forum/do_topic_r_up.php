<?php 

$auth = @$_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_GET['reply_id'])) die;

$reply_id = intval($_GET['reply_id']);

if (dt_query_one("SELECT id FROM forum_topic_reply_up WHERE reply_id = $reply_id AND user_id = ".$auth['id']." LIMIT 1")) die('已经喜欢过了！^_^');

$rs = dt_query("INSERT INTO forum_topic_reply_up (reply_id, user_id, c_at) VALUES ($reply_id, ".$auth['id'].", ".time().")"); 
if (!$rs) die('赞数据变更失败!');

$rs = dt_query("UPDATE forum_topic_reply SET up_c = up_c + 1 WHERE id = $reply_id");
if (!$rs) die('更新赞统计失败！');

do_topic_up_pay(dt_query_one("SELECT user_id FROM forum_topic_reply WHERE id = $reply_id")['user_id'], '回复被赞');

die('s0');
