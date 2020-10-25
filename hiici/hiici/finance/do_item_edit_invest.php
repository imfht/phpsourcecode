<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST['i_i_id'])) die;

$i_i_id = intval($_POST['i_i_id']);
$money = doubleval($_POST['money']);
$phone = doubleval($_POST['phone']);

if (empty($money) || empty($phone))  die('表单内容不规范！'); 

$rs = dt_query("SELECT user_id FROM finance_item_invest WHERE id = $i_i_id");
$i_i = mysql_fetch_array($rs);
if ($auth['id'] != $i_i['user_id']) die('违规操作！数据不属于您');

$rs = dt_query("UPDATE finance_item_invest SET money = $money, phone = $phone WHERE id = $i_i_id"); 
if (!$rs) die('数据修改失败!');

die('s0');

