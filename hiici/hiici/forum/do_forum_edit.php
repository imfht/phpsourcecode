<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST['forum_id'])) die;

$forum_id = intval($_POST['forum_id']);
$b_url = filter_var($_POST['b_url'], FILTER_SANITIZE_URL);
$url = filter_var($_POST['url'], FILTER_SANITIZE_URL);
$auto_bg_url = empty($_POST['auto_bg_url']) ? 0 : 1;
$background_url = filter_var($_POST['background_url'], FILTER_SANITIZE_URL);
$auto_intro = empty($_POST['auto_intro']) ? 0 : 1;
$intro = filter_var($_POST['intro'], FILTER_SANITIZE_STRING);
$pub_ads = cleanjs($_POST['pub_ads']);
$topic_limit = intval($_POST['topic_limit']);

require_once('inc/forum_city.php');
$city_user_id = dt_query_one("SELECT user_id FROM forum_city_info WHERE id = $forum_city")['user_id'];

global $config;
if (!dt_query_one("SELECT id FROM forum WHERE id = $forum_id AND user_id = ".$auth['id']." LIMIT 1") 
	&& !in_array($auth['id'], $config['manager']) 
	&& $city_user_id != $auth['id']) die('没有权限！^_^');

if (in_array($auth['id'], $config['manager']) || $city_user_id == $auth['id']) {
	$name = get_substr(filter_var($_POST['name'], FILTER_SANITIZE_STRING), 20);
	if (dt_query_one("SELECT id FROM forum WHERE city = '$forum_city' AND name = '$name' AND id != $forum_id")) die(json_encode(array('msg' => '该名称的板块在这个城市已经存在！^_^', 'token' => get_token())));

	$kind = intval($_POST['kind']);
	$update_add = ", name = '$name', kind = '$kind'";
} 
$rs = dt_query("UPDATE forum SET b_url = '$b_url', url = '$url', auto_bg_url = '$auto_bg_url', background_url = '$background_url', auto_intro='$auto_intro', intro = '$intro', pub_ads = '$pub_ads', topic_limit = '$topic_limit' ".@$update_add." WHERE id = $forum_id");
if (!$rs) die('更新数据失败！');

die('s0');
