<?php

$auth = $_SESSION['auth'];
if (empty($auth)) {
	header('Location:?c=user&a=login');
	die;
}

if (empty($_GET['forum_id'])) die;

$forum_id = intval($_GET['forum_id']);

if (0 < dt_count('forum_follow', "WHERE forum_id = $forum_id AND user_id =".$_SESSION['auth']['id'])) {
	$rs = dt_query("DELETE FROM forum_follow WHERE forum_id = $forum_id AND user_id = ".$auth['id']);
	if (!$rs) die('删除数据失败！');
	put_info('取消关注成功！^_^');
} else {
	$rs = dt_query("INSERT INTO forum_follow (forum_id, user_id, c_at) VALUES ($forum_id, ".$auth['id'].", ".time().")");
	if (!$rs) die('新增数据失败！');
	put_info('关注成功！^_^');
}

header('Location:?c=forum&a=topic_list&forum_id='.$forum_id);
die;
