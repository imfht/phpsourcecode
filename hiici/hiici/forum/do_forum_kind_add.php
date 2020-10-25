<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

require_once('inc/forum_city.php');

global $config;
if (!in_array($auth['id'], $config['manager']) && dt_query_one("SELECT user_id FROM forum_city_info WHERE id = $forum_city")['user_id'] != $auth['id']) die('用户权限不够!^_^');

if (empty($_POST)) die;

$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);

if (empty($name)) die('空的名称！');

$kind = dt_query_one("SELECT id FROM forum_kind WHERE city = '$forum_city' AND name = '$name'");
if ($kind) die(json_encode(array('msg' => '该分类在这个城市已经存在！^_^', 'token' => get_token())));

$rs = dt_query("INSERT INTO forum_kind (name, city, c_at) VALUES ('$name', '$forum_city', ".time().")");
if (!$rs) die('新建数据失败！');

die('s0');
