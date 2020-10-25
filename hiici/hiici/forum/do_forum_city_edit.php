<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

require_once('inc/forum_city.php');

global $config;
if (!in_array($auth['id'], $config['manager']) && dt_query_one("SELECT user_id FROM forum_city_info WHERE id = $forum_city")['user_id'] != $auth['id']) die('用户权限不够!^_^');

if (empty($_POST)) die;

$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$background_url = do_img_url_filter($_POST['background_url']);
$index_img_url = filter_var($_POST['index_img_url'], FILTER_SANITIZE_URL);
$index_img_link = filter_var($_POST['index_img_link'], FILTER_SANITIZE_URL);
$carousel = empty($_POST['carousel']) ? 0 : 1;
$top_ads = cleanjs($_POST['top_ads']);
$pub_ads = cleanjs($_POST['pub_ads']);
$g_t = empty($_POST['g_t']) ? 0 : 1;

if (empty($name)) die('空的内容!^_^');

if(in_array($auth['id'], $config['manager'])) {
	$user_id = intval($_POST['user_id']);
	$update_add = ", user_id = $user_id";
} 
$rs = dt_query("UPDATE forum_city_info SET name = '$name', background_url = '$background_url', index_img_url = '$index_img_url', index_img_link = '$index_img_link', carousel = '$carousel', top_ads = '$top_ads', pub_ads = '$pub_ads', g_t = '$g_t' ".@$update_add." WHERE id = $forum_city");
if (!$rs) die('更新数据失败！');

die('s0');
