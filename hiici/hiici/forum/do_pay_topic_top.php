<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die(json_encode(array('msg' => '用户未登录！^_^', 'token' => get_token())));

if (empty($_POST['topic_id'])) die;

$topic_id = intval($_POST['topic_id']);
$pay = doubleval($_POST['pay']);
$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

if (1 > $pay) die(json_encode(array('msg' => '付费置顶的最低支付金额为1元！^_^', 'token' => get_token())));

switch (account_pay_pre_check($password, $pay)) {
case -1: die(json_encode(array('msg' => '支付密码错误！^_^', 'token' => get_token())));
case -2: die(json_encode(array('msg' => '可用账户余额不足！^_^', 'token' => get_token())));
}

//执行置顶---->

$rs = dt_query("UPDATE forum_topic SET pay = pay + $pay, top_at = CASE WHEN top_at > ".time()." THEN top_at + ".(60*8*$pay)." ELSE ".(time()+(60*8*$pay))." END, digest = 1, c_at = ".time()." WHERE id = $topic_id");
if(!$rs) die("更新forum_topic数据失败！^_^");

//成功置顶后支付--->

$forum_city = dt_query_one("SELECT city FROM forum WHERE id = (SELECT forum_id FROM forum_topic WHERE id = $topic_id)")['city'];

if (!account_pay($pay, '付费置顶', 0, $forum_city)) die('支付失败！^_^');

die('s0');
