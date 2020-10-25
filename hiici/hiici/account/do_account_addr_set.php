<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST['addr'])) die('空的内容！^_^');

$addr = filter_var($_POST['addr'], FILTER_SANITIZE_STRING);
$p_code = intval($_POST['p_code']);
$name = get_substr(filter_var($_POST['name'], FILTER_SANITIZE_STRING), 20);
$phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);

$rs = dt_query("UPDATE account_addr SET name = '$name', addr = '$addr', p_code = $p_code, phone = '$phone' WHERE id = ".$auth['id']);
if (!$rs) die('数据变更失败！');

die('s0');
