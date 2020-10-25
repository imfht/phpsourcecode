<?php 

$auth = @$_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_POST['agreement'])) die(json_encode(array('msg' => '请确认同意“直播协议” ！^_^', 'token' => get_token()))); 

$n_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_SANITIZE_STRING);
$name = get_substr(filter_var($_POST['name'], FILTER_SANITIZE_STRING), 20);
$a_m = empty($_POST['a_m']) ? 0 : 1;
$l_m = empty($_POST['l_m']) ? 0 : 1;
$p_m = empty($_POST['p_m']) ? 0 : 1;
$pw_c = empty($_POST['password']) ? '' : "password = '".filter_var($_POST['password'], FILTER_SANITIZE_STRING)."',";

if (dt_query_one("SELECT id FROM flex_n_id WHERE name = '$name' AND user_id != '".$auth['id']."' LIMIT 1")) die(json_encode(array('msg' => '这个直播名已经被其他用户使用了！换一个吧。^_^', 'token' => get_token()))); 

$flex_n_id = dt_query_one("SELECT id FROM flex_n_id WHERE user_id = '".$auth['id']."'")['id'];
if (!$flex_n_id) {
	$rs = dt_query("INSERT INTO flex_n_id (user_id) VALUES ('".$auth['id']."')");
	if (!$rs) die('创建直播ip失败！^_^');

	$flex_n_id = dt_query_one("SELECT LAST_INSERT_ID()")[0];
}

$rs = dt_query("UPDATE flex_n_id SET name = '$name', n_ip = '$n_ip', a_m = '$a_m', l_m = '$l_m', p_m = '$p_m', $pw_c c_at = ".(time()-360)." WHERE id = '$flex_n_id'");
if (!$rs) die('刷新直播ip失败！^_^');

die('s0');
