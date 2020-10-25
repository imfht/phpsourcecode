<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_GET['shuo_reply_id'])) die;

$shuo_reply_id = intval($_GET['shuo_reply_id']);

if (0 < dt_count('shuo_reply_up', "WHERE shuo_reply_id = $shuo_reply_id AND user_id = ".$auth['id'])) die('已经赞过了！^_^');

$rs = dt_query("INSERT INTO shuo_reply_up (shuo_reply_id, user_id, c_at) VALUES ($shuo_reply_id, ".$auth['id'].", ".time().")"); 
if (!$rs) die('说说回复赞数据变更失败!');

$rs = dt_query("UPDATE shuo_reply SET up_c = up_c + 1 WHERE id = $shuo_reply_id");
if (!$rs) die('更新说说回复赞统计失败！');

$shuo_reply = dt_query_one("SELECT user_id, shuo_id FROM shuo_reply WHERE id = $shuo_reply_id");
if (!$shuo_reply) die('获取说说回复数据失败！');
$shuo = dt_query_one("SELECT content, user_name FROM shuo WHERE id = ".$shuo_reply['shuo_id']);
if (!$shuo) die('获取说说数据失败！');
if (!msg_sys_add('[<b>'.$auth['name'].'</b>] 在 [<b>'.$shuo['user_name'].'</b>] 的说说 <a href="?c=center&shuo_id='.$shuo_reply['shuo_id'].'">'.get_substr($shuo['content']).'</a> 里为您的回复点了赞', $shuo_reply['user_id'])) die('系统信息发送失败！');

do_topic_up_pay($shuo_reply['user_id'], '回复被赞');

die('s0');
