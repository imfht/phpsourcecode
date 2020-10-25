<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST['item_id'])) die;

$item_id = intval($_POST['item_id']);
$money = doubleval($_POST['money']);
$phone = doubleval($_POST['phone']);
$user_id = $auth['id'];
$user_name = $auth['name'];

if (empty($money) || empty($phone)) {
	put_info('表单内容不规范！');
	header('Location:?c=finance&a=item_invest&item_id='.$item_id);
	die;
}

$i_i = dt_query_one("SELECT id FROM finance_item_invest WHERE item_id = $item_id AND user_id = $user_id");
if ($i_i) {
	put_info('不能重复报价！');
	header('Location:?c=finance&a=item_show&item_id='.$item_id);
	die;
}

$rs = dt_query("INSERT INTO finance_item_invest (item_id, money, phone, user_id, user_name, c_at) 
	VALUES ('$item_id', '$money', '$phone', '$user_id', '$user_name', ".time().")");
if (!$rs) {
	put_info('提交申请失败！');
	header('Location:?c=finance&a=item_invest&item_id='.$item_id);
	die;
}

$rs = dt_query("UPDATE finance_item SET invest = invest + 1 WHERE id = $item_id");
if (!$rs) die('更新意向投资人数失败！');

put_info('成功提交报价！');
header('Location:?c=finance&a=item_show&item_id='.$item_id);
die;


