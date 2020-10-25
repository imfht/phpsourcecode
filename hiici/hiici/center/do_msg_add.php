<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET['user_id'])) die;

$user_id = intval($_GET['user_id']);
$content = cleanjs($_POST['content']);

if (empty($content)) die(json_encode(array('msg' => '空的内容！^_^', 'token' => get_token())));

$user_info = dt_query_one("SELECT name FROM user_info WHERE id = $user_id");
if (!$user_info) die('用户名获取失败！');

$rs = dt_query("INSERT INTO msg (content, user_id, user_name, to_user_id, to_user_name, c_at) VALUES ('$content', ".$auth['id'].", '".$auth['name']."', $user_id, '".$user_info['name']."',".time().")");
if (!$rs) die('发送失败！');

$msg_index = dt_query_one("SELECT user_id_a FROM msg_index WHERE (user_id_a = ".$auth['id']." AND user_id_b = $user_id) OR (user_id_a = $user_id AND user_id_b = ".$auth['id'].")");
if (!$msg_index) {
	$rs= dt_query("INSERT INTO msg_index (content, user_id_a, user_name_a, user_id_b, user_name_b, last_msg_c_at, c_at) 
		VALUES ('$content', ".$auth['id'].", '".$auth['name']."', $user_id, '".$user_info['name']."', ".time().", ".time().")");
	if (!$rs) die('新建数据失败！');
} else {
	if ($auth['id'] == $msg_index['user_id_a']) {
		$rs = dt_query("UPDATE msg_index SET content = '$content', user_name_a = '".$auth['name']."', user_name_b = '".$user_info['name']."', new_msg_c_b = new_msg_c_b + 1, last_msg_c_at = ".time()." WHERE user_id_a = ".$auth['id']." AND user_id_b = $user_id");
	} else {
		$rs = dt_query("UPDATE msg_index SET content = '$content', user_name_b = '".$auth['name']."', user_name_a = '".$user_info['name']."', new_msg_c_a = new_msg_c_a + 1, last_msg_c_at = ".time()." WHERE user_id_b = ".$auth['id']." AND user_id_a = $user_id");
	}
	if (!$rs) die('数据变更失败！');
}

die(json_encode(array('msg' => 's0', 'token' => get_token())));
