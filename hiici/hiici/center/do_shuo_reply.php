<?php 

$auth = @$_SESSION['auth'];
if (empty($auth)) die(json_encode(array('msg' => '用户未登录！^_^', 'token' => get_token())));

if (empty($_POST['shuo_id'])) die;

$shuo_id = intval($_POST['shuo_id']);
$to_user_id = intval($_POST['to_user_id']);
$content = cleanjs($_POST['content']);

if (empty($content)) die(json_encode(array('msg' => '空的内容！', 'token' => get_token())));

$rs = dt_query("INSERT INTO shuo_reply (content, shuo_id, user_id, user_name, c_at) 
	VALUES ('$content', $shuo_id, ".$auth['id'].", '".$auth['name']."', ".time().")"); 
if (!$rs) die('说说回复数据变更失败!');

$rs = dt_query("UPDATE shuo SET reply_c = reply_c + 1 WHERE id = $shuo_id");
if (!$rs) die('更新说说回复统计失败！');

if (empty($to_user_id)) {
	$shuo = dt_query_one("SELECT content, user_id FROM shuo WHERE id = $shuo_id");
	if (!$shuo) die('获取说说数据失败！');
	if (!msg_sys_add('[<b>'.$auth['name'].'</b>] 为您的说说 <a href="?c=center&shuo_id='.$shuo_id.'">'.get_substr($shuo['content']).'</a> 添加了新的回复', $shuo['user_id'])) die('系统信息发送失败！');
} else {
	$shuo = dt_query_one("SELECT content, user_name FROM shuo WHERE id = $shuo_id");
	if (!$shuo) die('获取说说数据失败！');
	if (!msg_sys_add('[<b>'.$auth['name'].'</b>] 在 [<b>'.$shuo['user_name'].'</b>] 的说说 <a href="?c=center&shuo_id='.$shuo_id.'">'.get_substr($shuo['content']).'</a> 里为您的回复添加了新的回复', $to_user_id)) die('系统信息发送失败！');
}

die(json_encode(array('msg' => 's0', 'token' => get_token())));
