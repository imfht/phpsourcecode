<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET['forum_id'])) die;

$forum_id = intval($_GET['forum_id']);

if (1 > dt_count('forum', "WHERE id = $forum_id AND change_manager = 1")) die('该板块没有换主！^_^');

$rs = dt_query("UPDATE forum SET user_id = ".$auth['id'].", user_name = '".$auth['name']."', change_manager = 0 WHERE id = $forum_id AND change_manager = 1");
if (!$rs) die('更新数据失败！');

put_info('成功获得管理权！^_^');
header('Location:?c=forum&a=topic_list&forum_id='.$forum_id);
die;
