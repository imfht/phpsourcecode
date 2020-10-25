<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST['new_name'])) die(json_encode(array('msg' => '空的内容！^_^', 'token' => get_token())));

$new_name = get_substr(filter_var($_POST['new_name'], FILTER_SANITIZE_STRING), 20);
$new_intro = filter_var($_POST['new_intro'], FILTER_SANITIZE_STRING);

if (dt_query_one("SECECT id FROM user_info WHERE name = '$new_name' AND id != ".$auth['id']." LIMIT 1")) die(json_encode(array('msg' => '这个名字已经被其他用户使用了！换一个吧。^_^', 'token' => get_token())));

$rs = dt_query("UPDATE user_info SET name = '$new_name', intro = '$new_intro' WHERE id = ".$auth['id']);
if (!$rs) die('数据变更失败！');

die('s0');
