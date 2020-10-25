<?php

//获取topic_id给后面的shuo_content用(上必须接forum_topic表insert操作)
$topic_id = dt_query_one("SELECT LAST_INSERT_ID()")[0];

//更新论坛图标
if (!empty($icon_url)) {
	$f_bg_url = $icon_url;
}

$bg_url_cond = (empty($forum['auto_bg_url']) || empty($f_bg_url)) ? '' : ", background_url = '$f_bg_url'";
//更新论坛简介为帖子标题
$intro_cond = (empty($forum['auto_intro'])) ? '' : ", intro = '$title'";

//发帖公共代码
if (0 != $forum['ext']) {
	require_once('forum/forum_ext/ext_'.$forum['ext'].'/do_topic_add.php');
} 

$rs = dt_query("UPDATE forum SET topic_c = topic_c + 1, reply_c = reply_c + 1 $bg_url_cond $intro_cond WHERE id = $forum_id");
if (!$rs) die('更新forum数据失败！');

if (0 < dt_count('forum', "WHERE id = $forum_id AND today = ".date('Ymd', time()))) {
	$rs = dt_query("UPDATE forum SET today_reply_c = today_reply_c + 1 WHERE id = $forum_id");
} else {
	$rs = dt_query("UPDATE forum SET today = ".date('Ymd', time()).", today_reply_c = 1, today_up_c = 0 WHERE id = $forum_id");
}
if (!$rs) die('更新today_reply_c数据失败！');

//如果auth不为空，在空间自动发布说说
$shuo_content = '<p>发布了 <a href="?c=forum&a=topic_show&topic_id='.$topic_id.'">'.get_substr($title, 20).'</a></p>'; 
if (!empty($_SESSION['auth'])) if (!shuo_add($shuo_content, $auth['id'], $auth['name'])) die('在do_topic_add_pub发布说说失败！^_^');
