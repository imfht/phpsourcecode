<?php

$topic_id = intval($_REQUEST['topic_id']);

$topic = dt_query_one("SELECT order_c_s, start_t, start_t_s, order_l, order_l_n, out_s FROM forum_topic WHERE id = $topic_id");
if (!$topic) die('获取topic数据失败！^_^');

//是否跳转
if (1 == $topic['out_s']) die('s2'); 

//用户是否登录
$auth = @$_SESSION['auth'];
if (empty($auth)) { 
	if (empty($_POST['tel'])) die('s3');  //留电话下单
	else {
		if (@$_SESSION['topic_order_t_time'] > time()) die('下单过于频繁了哟！^_^');

		$tel = filter_var($_POST['tel'], FILTER_SANITIZE_STRING);

		$_SESSION['topic_order_t_time'] = time()+60;
		$has_c_order = 1;
		$has_c_order_t = (dt_query_one("SELECT id FROM forum_topic_order WHERE topic_id = $topic_id AND tel != '' AND complete = 0 LIMIT 1")) ? 1 : 0;   //是否有未完成的电话订单
	}
} else {
	if (!check_account_addr($auth['id'])) die('e1');

	$cond = "WHERE user_id = ".$auth['id']." AND topic_id = $topic_id AND complete = 0";
	$has_c_order = (dt_query_one("SELECT id FROM forum_topic_order ".preg_replace('/complete = 0/', 'complete = 1', $cond, 1)." LIMIT 1")) ? 1 : 0;   //是否有已完成的订单，仿刷订单
	$has_c_order_t = 0;
}

if (empty($auth) || !dt_query_one("SELECT id FROM forum_topic_order $cond LIMIT 1")) {
	if (1 == $topic['start_t'] && time() < $topic['start_t_s']) die('违规的操作，未开始！^_^');    //是否开放下单
	if (2 == $topic['start_t'] && time() > $topic['start_t_s']) die('违规的操作，已结束！^_^');    //是否结束下单
	if (1 == $topic['order_l'] && $topic['order_c_s'] >= $topic['order_l_n']) die('订单已达上限！^_^');    //是否达到订单上限

	$num = @doubleval($_POST['num']);
	if (empty($num)) die('s3');  

	if (empty($auth)) $rs = dt_query("INSERT INTO forum_topic_order (topic_id, tel, num, c_at) VALUES ('$topic_id', '$tel', '$num', ".time().")");
	else $rs = dt_query("INSERT INTO forum_topic_order (user_id, topic_id, num, c_at) VALUES (".$auth['id'].", $topic_id, '$num', ".time().")");
	if (!$rs) die('新增forum_topic_order数据失败！');

	$rs = dt_query("UPDATE forum_topic SET order_c = CASE WHEN $has_c_order_t = 0 THEN order_c + 1 ELSE order_c END, order_c_s = CASE WHEN $has_c_order = 0 THEN order_c_s + 1 ELSE order_c_s END WHERE id = $topic_id");
	if (!$rs) die('统计forum_topic数据失败！');
	die('s0');
} else {
	if (empty($auth)) die('成功下单！^_^');

	$rs = dt_query("DELETE FROM forum_topic_order $cond");
	if (!$rs) die('删除forum_topic_order数据失败！');

	$rs = dt_query("UPDATE forum_topic SET order_c = order_c - 1, order_c_s = CASE WHEN $has_c_order = 0 THEN order_c_s - 1 ELSE order_c_s END WHERE id = $topic_id");
	if (!$rs) die('统计forum_topic数据失败！');
	die('s1');
}
