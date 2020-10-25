<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST['password_new'])) die('空的内容！^_^');

$password_old = $_POST['password_old'];
$password_new = $_POST['password_new'];
$password_new_r = $_POST['password_new_r'];

if (1 > dt_count('account', "WHERE id = ".$auth['id']." AND password = '".sha1($password_old)."'")) die(json_encode(array('msg' => '旧支付密码错误！^_^', 'token' => get_token())));;

if ($password_new != $password_new_r) die(json_encode(array('msg' => '新密码不一致！^_^', 'token' => get_token())));

$rs = dt_query("UPDATE account SET password = '".sha1($password_new)."' WHERE id = ".$auth['id']);
if (!$rs) die('数据变更失败！');

die('s0');
