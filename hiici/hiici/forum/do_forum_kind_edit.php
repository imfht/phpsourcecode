<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

require_once('inc/forum_city.php');

global $config;
if (!in_array($auth['id'], $config['manager']) && dt_query_one("SELECT user_id FROM forum_city_info WHERE id = $forum_city")['user_id'] != $auth['id']) die('用户权限不够!^_^');

if (empty($_POST)) die;

$kind_id = intval($_POST['kind_id']);
$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);

if (empty($name)) die('空的名称！');

$rs = dt_query("UPDATE forum_kind SET name = '$name' WHERE id = $kind_id");
if (!$rs) die('更新数据失败！');

die('s0');
