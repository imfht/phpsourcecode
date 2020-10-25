<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST)) die;

$name_real = get_substr(filter_var($_POST['name_real'], FILTER_SANITIZE_STRING), 20);
$sex = @filter_var($_POST['sex'], FILTER_SANITIZE_STRING);
$local = filter_var($_POST['local'], FILTER_SANITIZE_STRING);
$birthday = filter_var($_POST['birthday'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
$qq = doubleval($_POST['qq']);
$mobile = doubleval($_POST['mobile']);

if (empty($name_real) || empty($sex)) die(json_encode(array('msg' => '空的内容！^_^', 'token' => get_token())));

$cond = "WHERE id = ".$auth['id'];

if (1 > dt_count('archive', $cond)) die('没有您的数据！^_^');

$rs = dt_query("UPDATE archive SET name_real = '$name_real', sex = '$sex', local= '$local', birthday= '$birthday', email = '$email', qq = '$qq', mobile = '$mobile' $cond");
if (!$rs) die('更新数据失败！');

die('s0');
