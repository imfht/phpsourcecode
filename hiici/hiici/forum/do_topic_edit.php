<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_POST['topic_id'])) die;

global $config;
$topic_id = intval($_POST['topic_id']);
$is_s_m = in_array($auth['id'], $config['manager']);
$title = ($is_s_m) ? $_POST['title'] : get_substr(filter_var($_POST['title'], FILTER_SANITIZE_STRING), 40);
$icon_url = filter_var($_POST['icon_url'], FILTER_SANITIZE_STRING);
$content = ($is_s_m) ? $_POST['content'] : cleanjs($_POST['content']); //超级管理员不过滤js
$orders = empty($_POST['orders']) ? 0 : doubleval($_POST['orders_p']);
require_once('inc/geo_opr.php');
$geo = empty($geo) ? '' : ", geo = '$geo'";
$update_x = '';

if (0 < dt_query_one("SELECT pay FROM forum_topic WHERE id = $topic_id")['pay']) {
	if (!dt_query_one("SELECT id FROM forum_topic WHERE id = $topic_id AND user_id = ".$auth['id']." LIMIT 1") 
		&& !in_array($auth['id'], $config['manager'])) die(json_encode(array('msg' => '没有权限！^_^', 'token' => get_token())));

	$start_t = (0 < $orders) ? intval($_POST['start_t']) : 0 ;
	$start_t_s = (1 == $start_t || 2 == $start_t) ? strtotime($_POST['start_t_s']) : 0 ;
	$order_l = (0 < $orders) ? intval($_POST['order_l']) : 0 ;
	$order_l_n = (1 == $order_l) ? intval($_POST['order_l_n']) : 0 ;
	$out_s = (0 < $orders) ? intval($_POST['out_s']) : 0 ;
	$out_s_u = (1 == $out_s) ? filter_var($_POST['out_s_u'], FILTER_SANITIZE_URL) : 0 ;

	$update_x = ", start_t = '$start_t', start_t_s = '$start_t_s', order_l = '$order_l',".((empty($order_l)) ? '' :" order_l_n = '$order_l_n',")." out_s = '$out_s', out_s_u = '$out_s_u'";

} else {
	require_once('inc/forum_city.php');
	global $config;
	if (!dt_query_one("SELECT id FROM forum_topic WHERE id = $topic_id AND user_id = ".$auth['id']." LIMIT 1") 
		&& !dt_query_one("SELECT id FROM forum WHERE id = (SELECT forum_id FROM forum_topic WHERE id = $topic_id) AND user_id = ".$auth['id']." LIMIT 1") 
		&& !in_array($auth['id'], $config['manager'])
		&& dt_query_one("SELECT user_id FROM forum_city_info WHERE id = $forum_city")['user_id'] != $auth['id']) die('没有权限！^_^');
}

if (empty($title) || empty($content)) die('空的标题或内容！');

$rs = dt_query("UPDATE forum_topic SET title = '$title', icon_url = '$icon_url', content = '$content', orders = '$orders' $geo $update_x WHERE id = $topic_id");
if (!$rs) die('更新数据失败！');

$forum_ext = dt_query_one("SELECT ext FROM forum WHERE id = (SELECT forum_id FROM forum_topic WHERE id = $topic_id)")['ext'];
if (0 != $forum_ext) {
	require_once('forum_ext/ext_'.$forum_ext.'/do_topic_edit.php');
} 

die('s0');
