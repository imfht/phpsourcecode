<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET['msg_id'])) die;

$msg_id = intval($_GET['msg_id']);

$cond = "WHERE id = $msg_id AND user_id = ".$auth['id'];

if (1 > dt_count('msg', $cond)) die('违规删除非属私信！');

$rs = dt_query("DELETE FROM msg $cond"); 
if (!$rs) die('删除数据失败！');

die('s0');
