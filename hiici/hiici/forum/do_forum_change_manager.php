<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET['forum_id'])) die;

$forum_id = intval($_GET['forum_id']);

global $config;
if (1 > dt_count('forum', "WHERE id = $forum_id AND user_id = ".$auth['id']) && !in_array($auth['id'], $config['manager'])) die('没有权限！^_^');

$rs = dt_query("UPDATE forum SET change_manager = 1 WHERE id = $forum_id");
if (!$rs) die('更新数据失败！');

put_info('设置成功！^_^');
header('Location:?c=forum&a=topic_list&forum_id='.$forum_id);
die;
