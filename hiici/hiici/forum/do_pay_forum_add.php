<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST)) die;

$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$background_url = filter_var($_POST['background_url'], FILTER_SANITIZE_URL);
$intro = filter_var($_POST['intro'], FILTER_SANITIZE_STRING);
$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
$pay = 500;

if (empty($name)) die('空的内容！^_^');

require_once('inc/forum_city.php');
$forum = dt_query_one("SELECT id FROM forum WHERE city = '$forum_city' AND name = '$name'");
if ($forum) die(json_encode(array('msg' => '该板块在这个城市已经存在！^_^', 'token' => get_token())));

switch (account_pay_pre_check($password, $pay)) {
case -1: die(json_encode(array('msg' => '支付密码错误！^_^', 'token' => get_token())));
case -2: die(json_encode(array('msg' => '可用账户余额不足！^_^', 'token' => get_token())));
}

//执行创建---->

require_once('inc/do_forum_add_pub.php');

//成功发帖后支付--->

if (!account_pay($pay, '付费创建板块', 0, $forum_city)) die('支付失败！^_^');

die('s0');
