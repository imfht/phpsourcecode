<?php 

$auth = @$_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_GET['shuo_id'])) die;

$shuo_id = intval($_GET['shuo_id']);

if (0 < dt_count('shuo_up', "WHERE shuo_id = $shuo_id AND user_id = ".$auth['id'])) die('已经赞过了！^_^');

$rs = dt_query("INSERT INTO shuo_up (shuo_id, user_id, c_at) VALUES ($shuo_id, ".$auth['id'].", ".time().")"); 
if (!$rs) die('说说赞数据变更失败!');

$rs = dt_query("UPDATE shuo SET up_c = up_c + 1 WHERE id = $shuo_id");
if (!$rs) die('更新说说赞统计失败！');

$shuo = dt_query_one("SELECT content, user_id FROM shuo WHERE id = $shuo_id");
if (!$shuo) die('获取说说数据失败！');
if (!msg_sys_add('[<b>'.$auth['name'].'</b>] 为您的说说 <a href="?c=center&shuo_id='.$shuo_id.'">'.get_substr($shuo['content']).'</a> 点了赞', $shuo['user_id'])) die('系统信息发送失败！');

do_topic_up_pay($shuo['user_id'], '说说被赞');

die('s0');
