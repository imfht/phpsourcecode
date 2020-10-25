<?php 

$auth = @$_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_GET['topic_id'])) die;

$topic_id = intval($_GET['topic_id']);
$t_u_cond = "WHERE topic_id = $topic_id AND user_id = ".$auth['id'];

if (0 < dt_count('forum_topic_up', $t_u_cond)) {
	dt_query("UPDATE forum_topic_up SET c_at = ".time()." $t_u_cond"); die('已经喜欢过了，时间已重置！^_^');
}

$rs = dt_query("INSERT INTO forum_topic_up (topic_id, user_id, c_at) VALUES ($topic_id, ".$auth['id'].", ".time().")"); 
if (!$rs) die('赞数据变更失败!');

//forum_topic 的 today_up_c
$rs = dt_query("UPDATE forum_topic SET up_c = up_c + 1, l_r_at = ".time()." WHERE id = $topic_id");
if (!$rs) die('更新赞统计失败！');

if (0 < dt_count('forum_topic', "WHERE id = $topic_id AND today = ".date('Ymd', time()))) {
	$rs = dt_query("UPDATE forum_topic SET today_up_c = today_up_c + 1 WHERE id = $topic_id");
} else {
	$rs = dt_query("UPDATE forum_topic SET today = ".date('Ymd', time()).", today_up_c = 1 WHERE id = $topic_id");
}
if (!$rs) die('更新topic的today_up_c数据失败！');

//由用户赞帖来驱动更新forum_kind的today_up_c
$forum = dt_query_one("SELECT id, city FROM forum WHERE id = (SELECT forum_id FROM forum_topic WHERE id = $topic_id)");

$cond = "WHERE city = ".$forum['city'];
if (!dt_query_one("SELECT id FROM forum_kind $cond AND today = ".date('Ymd', time())." LIMIT 1")) {
	$kinds = dt_query("SELECT id FROM forum_kind $cond");
	if (!$kinds) die('获取kind数据失败！');

	while($kind = mysql_fetch_array($kinds)) {
		$kind_today_up_c = dt_sum('forum', 'today_up_c', "WHERE kind = ".$kind['id']) / dt_count('forum', "WHERE kind = ".$kind['id']);
		$rs = dt_query("UPDATE forum_kind SET today = ".date('Ymd', time()).", today_up_c = $kind_today_up_c WHERE id = ".$kind['id']);
		if (!$rs) die('更新kind_today_up_c数据失败！');
	}
} 

//forum 的 today_up_c
if (0 < dt_count('forum', "WHERE id = ".$forum['id']." AND today = ".date('Ymd', time()))) {
	$rs = dt_query("UPDATE forum SET today_up_c = today_up_c + 1 WHERE id = ".$forum['id']);
} else {
	$rs = dt_query("UPDATE forum SET today = ".date('Ymd', time()).", today_reply_c = 0, today_up_c = 1 WHERE id = ".$forum['id']);
}
if (!$rs) die('更新today_up_c数据失败！');

do_topic_up_pay(dt_query_one("SELECT user_id FROM forum_topic WHERE id = $topic_id")['user_id'], '主题被赞');

die('s0');
