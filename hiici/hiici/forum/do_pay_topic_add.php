<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST['forum_id'])) die;

$forum_id = intval($_POST['forum_id']);
$title = get_substr(filter_var($_POST['title'], FILTER_SANITIZE_STRING), 40);
$icon_url = filter_var($_POST['icon_url'], FILTER_SANITIZE_STRING);
$content = cleanjs($_POST['content']);
$orders = empty($_POST['orders']) ? 0 : doubleval($_POST['orders_p']);
require_once('inc/geo_opr.php');

$start_t = (0 < $orders) ? intval($_POST['start_t']) : 0 ;
$start_t_s = (1 == $start_t || 2 == $start_t) ? strtotime($_POST['start_t_s']) : 0 ;
$order_l = (0 < $orders) ? intval($_POST['order_l']) : 0 ;
$order_l_n = (1 == $order_l) ? intval($_POST['order_l_n']) : 0 ;
$out_s = (0 < $orders) ? intval($_POST['out_s']) : 0 ;
$out_s_u = (1 == $out_s) ? filter_var($_POST['out_s_u'], FILTER_SANITIZE_URL) : 0 ;

$pay = doubleval($_POST['pay']);
$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

if (1 > $pay) die(json_encode(array('msg' => '付费发布的最低支付金额为1元！^_^', 'token' => get_token())));

switch (account_pay_pre_check($password, $pay)) {
case -1: die(json_encode(array('msg' => '支付密码错误！^_^', 'token' => get_token())));
case -2: die(json_encode(array('msg' => '可用账户余额不足！^_^', 'token' => get_token())));
}

//执行发帖---->

if (empty($title) || empty($content)) die('空的标题或内容！');

$forum = dt_query_one("SELECT auto_bg_url, auto_intro, city, ext FROM forum WHERE id = $forum_id");
if(!$forum) die('获取forum数据失败！^_^');

$rs = dt_query("INSERT INTO forum_topic (forum_id, title, icon_url, content, user_id, user_name, l_r_user_id, l_r_user_name, l_r_at, digest, geo, pay, top_at, city, orders, start_t, start_t_s, order_l, order_l_n, out_s, out_s_u, c_at) 
	VALUES ('$forum_id', '$title', '$icon_url', '$content', ".$auth['id'].", '".$auth['name']."', ".$auth['id'].", '".$auth['name']."', ".time().", 1, '$geo', $pay, ".(time() + 60*8*$pay).", ".$forum['city'].", $orders, '$start_t', '$start_t_s', '$order_l', '$order_l_n', '$out_s', '$out_s_u', ".time().")");
if (!$rs) die('新建forum_topic数据失败！');

require_once('inc/do_topic_add_pub.php');

//成功发帖后支付--->

if (!account_pay($pay, '付费发布', 0, $forum['city'])) die('支付失败！^_^');

die('s0');
