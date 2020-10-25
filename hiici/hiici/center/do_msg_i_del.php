<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_GET['msg_i_id'])) die;

$msg_i_id = doubleval($_GET['msg_i_id']);

$cond = "WHERE id = $msg_i_id AND (user_id_a = ".$auth['id']." OR user_id_b = ".$auth['id'].")";

if (!dt_query_one("SELECT id FROM msg_index ".$cond." LIMIT 1")) die('违规删除非属私信！^_^');

$rs = dt_query("DELETE FROM msg_index $cond"); 
if (!$rs) die('删除数据失败！^_^');

die('s0');
