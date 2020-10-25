<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (!empty($_POST)) die;

$order_id = @intval($_GET['order_id']);
if (!empty($order_id)) {
	$cond = "WHERE id = '$order_id' AND complete = 0";
} else {
	$topic_id = intval($_GET['topic_id']);
	$user_id = intval($_GET['user_id']);
	$cond = "WHERE topic_id = '$topic_id' AND user_id = '$user_id' AND complete = 0";
}

$order = dt_query_one("SELECT id, topic_id FROM forum_topic_order $cond");
if (!$order) die('获取forum_topic_order数据失败！^_^');

if (!dt_query_one("SELECT user_id FROM forum_topic WHERE id = '".$order['topic_id']."' AND user_id = '".$auth['id']."'")) die("错误的操作！^_^");   //检查订单所属

$rs = dt_query("UPDATE forum_topic_order SET complete = 1 WHERE id = '".$order['id']."'");
if (!$rs) die('更新forum_topic_order数据失败！^_^'); 

//变更订单量提示
$rs = dt_query("UPDATE forum_topic SET order_c = order_c - 1 WHERE id = ".$order['topic_id']);
if (!$rs) die('统计forum_topic数据失败！');

die('s0');
