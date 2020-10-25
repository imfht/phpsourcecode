<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

require_once('inc/forum_city.php');

global $config;
if (!in_array($auth['id'], $config['manager']) && dt_query_one("SELECT user_id FROM forum_city_info WHERE id = $forum_city")['user_id'] != $auth['id']) die(json_encode(array('msg' => '用户权限不够!^_^', 'token' => get_token())));

if (empty($_POST)) die;

$name = get_substr(filter_var($_POST['name'], FILTER_SANITIZE_STRING), 20);
$background_url = filter_var($_POST['background_url'], FILTER_SANITIZE_URL);
$intro = filter_var($_POST['intro'], FILTER_SANITIZE_STRING);

if (empty($name)) die('空的内容！^_^');

$forum = dt_query_one("SELECT id FROM forum WHERE city = '$forum_city' AND name = '$name'");
if ($forum) die(json_encode(array('msg' => '该板块在这个城市已经存在！^_^', 'token' => get_token())));

require_once('inc/do_forum_add_pub.php');

die('s0');
