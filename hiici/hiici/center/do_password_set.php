<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if(is_qq_user($auth['id'])) die(json_encode(array('msg' => '您是QQ用户！^_^', 'token' => get_token())));
if (empty($_POST['password_new'])) die(json_encode(array('msg' => '空的内容！^_^', 'token' => get_token())));

$password_old = $_POST['password_old'];
$password_new = $_POST['password_new'];
$password_new_r = $_POST['password_new_r'];

if (1 > dt_count('user', "WHERE id = ".$auth['id']." AND password = '".sha1($password_old)."'")) die(json_encode(array('msg' => '旧密码错误！^_^', 'token' => get_token())));

if ($password_new != $password_new_r) die(json_encode(array('msg' => '新密码不一致！^_^', 'token' => get_token())));

$rs = dt_query("UPDATE user SET password = '".sha1($password_new)."' WHERE id = ".$auth['id']);
if (!$rs) die('数据变更失败！');

die('s0');
