<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

require_once('inc/forum_city.php');

global $config;
if (!in_array($auth['id'], $config['manager']) && dt_query_one("SELECT user_id FROM forum_city_info WHERE id = $forum_city")['user_id'] != $auth['id']) die('用户权限不够!^_^');

if (empty($_POST)) die;

$carousel_id = intval($_POST['carousel_id']);
$img_url = filter_var($_POST['img_url'], FILTER_SANITIZE_URL);
$img_link = filter_var($_POST['img_link'], FILTER_SANITIZE_URL);

if (empty($img_url)) die('空的内容！^_^');

$rs = dt_query("UPDATE forum_carousel SET img_url = '$img_url', img_link = '$img_link', m_at = '".time()."' WHERE id = $carousel_id");
if (!$rs) die('更新数据失败！^_^');

die('s0');
