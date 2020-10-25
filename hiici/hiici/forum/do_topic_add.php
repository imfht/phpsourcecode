<?php

require_once('inc/forum_city.php');
$city_info = dt_query_one("SELECT user_id, g_t FROM forum_city_info WHERE id = $forum_city");

$auth = @$_SESSION['auth'];
if (empty($auth)) (1 == $city_info['g_t']) ? $auth = array('id' => 6, 'name' => '游客') : die(json_encode(array('msg' => '用户未登录！^_^', 'token' => get_token())));  //如未登录以游客身份发帖。

if (empty($_POST['forum_id'])) die;
if (time()-300 < dt_query_one("SELECT c_at FROM forum_topic WHERE user_id = ".$auth['id']." ORDER BY c_at DESC LIMIT 1")['c_at']) die(json_encode(array('msg' => '发布的过于频繁！^_^', 'token' => get_token())));

$forum_id = intval($_POST['forum_id']);
$title = get_substr(filter_var($_POST['title'], FILTER_SANITIZE_STRING), 40);
$icon_url = filter_var($_POST['icon_url'], FILTER_SANITIZE_STRING);
$content = cleanjs($_POST['content']);
$orders = empty($_POST['orders']) ? 0 : doubleval($_POST['orders_p']);
require_once('inc/geo_opr.php');

$forum = dt_query_one("SELECT auto_bg_url, auto_intro, topic_limit, city, ext FROM forum WHERE id = $forum_id");
if (!$forum) die('获取forum数据失败！');

global $config;
if (!in_array($auth['id'], $config['manager']) && $city_info['user_id'] != $auth['id']) {
	switch ($forum['topic_limit']) {
	case 1:
		if (1 > dt_count('forum', "WHERE id = $forum_id AND user_id = ".$auth['id'])) die(json_encode(array('msg' => '该板块仅限版主发帖！^_^', 'token' => get_token())));
		break;
	case 2:
		if (0 < dt_count('forum_topic', "WHERE forum_id = $forum_id AND user_id = ".$auth['id']." AND c_at > ".(time() - 3600*24))) die(json_encode(array('msg' => '该板块内每天只能发一贴！^_^', 'token' => get_token())));
	}
}

if (empty($title) || empty($content)) die('空的标题或内容！');

$rs = dt_query("INSERT INTO forum_topic (forum_id, title, icon_url, content, user_id, user_name, l_r_user_id, l_r_user_name, l_r_at, top_at, geo, city, orders, c_at) 
	VALUES ('$forum_id', '$title', '$icon_url', '$content', ".$auth['id'].", '".$auth['name']."', ".$auth['id'].", '".$auth['name']."', ".time().", ".time().", '$geo', ".$forum['city'].", $orders, ".time().")");
if (!$rs) die('新建forum_topic数据失败！');

require_once('inc/do_topic_add_pub.php');

die('s0');
