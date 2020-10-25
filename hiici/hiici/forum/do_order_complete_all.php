<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (!empty($_POST)) die;

$topic_id = @intval($_GET['topic_id']);
if (empty($topic_id)) { 
	$user_id = intval($_GET['user_id']);

	$orders = dt_query("SELECT id FROM forum_topic_order WHERE user_id = $user_id AND complete = 0 AND topic_id IN (SELECT id FROM forum_topic WHERE user_id = ".$auth['id']." AND id IN (SELECT topic_id FROM forum_topic_order WHERE user_id = $user_id AND complete = 0))");
	if (!$orders) die('获取forum_topic_orders数据失败！^_^');

} else {
	$page = intval($_GET['page']);

	if (!dt_query_one("SELECT user_id FROM forum_topic WHERE id = '$topic_id' AND user_id = '".$auth['id']."'")) die("错误的操作！^_^");   //检查订单所属

	if (empty($page)) $page = 1; 
	$limit = require_once('inc/order_search_limit.php');

	$orders = dt_query("SELECT id FROM forum_topic_order WHERE topic_id = '$topic_id' AND complete = 0 ORDER BY complete, c_at DESC LIMIT ".$limit * ($page - 1).",$limit");
	if (!$orders) die('获取forum_topic_orders数据失败！^_^');

}

//全部标记已经发货并变更订单量提示
$c_o_ids = '';
$order_c_chg = 0;
while($order = mysql_fetch_array($orders)) {
	$c_o_ids .= empty($c_o_ids) ? $order['id'] : ', '.$order['id'];
	$order_c_chg++;
}
if (0 < $order_c_chg) {
	if (!empty($topic_id)) {
		$g_o_c = dt_count('forum_topic_order', "WHERE topic_id = '$topic_id' AND tel != '' AND complete = 0 ORDER BY complete, c_at DESC LIMIT ".$limit * ($page - 1).",$limit");
		$order_c_chg -= (1 < $g_o_c) ? $g_o_c-1 : 0;
	}

	$rs = dt_query("UPDATE forum_topic_order SET complete = 1 WHERE id IN ($c_o_ids)");
	if (!$rs) die('更新forum_topic_order数据失败！^_^'); 

	$t_ids = dt_query("SELECT topic_id FROM forum_topic_order WHERE id IN ($c_o_ids)");
	while($t_id = mysql_fetch_array($t_ids)) @$t_ids_s .= ($t_ids_s) ? ','.$t_id['topic_id'] : $t_id['topic_id'];
	
	if (empty($topic_id)) $rs = dt_query("UPDATE forum_topic SET order_c = order_c - 1 WHERE id IN ($t_ids_s)");
	else $rs = dt_query("UPDATE forum_topic SET order_c = order_c - $order_c_chg WHERE id = $topic_id");

	if (!$rs) die('变更订单量order_c失败！^_^');
}

die('s0');
