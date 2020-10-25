<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST['des'])) die;

$des= filter_var($_POST['des'], FILTER_SANITIZE_STRING);
$money = doubleval($_POST['money']);
$phone = doubleval($_POST['phone']);
$lmcy_id = intval($_POST['lmcy_id']);
$user_id = $auth['id'];
$user_name = $auth['name'];

if (empty($money) || empty($phone)) {
	put_info('表单内容不规范！');
	header('Location:?c=finance&a=pre_invest');
	die;
}

if (1 > dt_count('finance_pre_invest', "WHERE user_id = $user_id")) {
	$rs = dt_query("INSERT INTO finance_pre_invest (user_id, user_name, des, money, phone, lmcy_id, m_at, c_at) 
		VALUES ($user_id, '$user_name', '$des', $money, $phone, $lmcy_id, ".time().", ".time().")");
	if (!$rs) die('提交失败！^_^');
} else {
	$rs = dt_query("UPDATE finance_pre_invest SET des= '$des', money = $money, phone = $phone, lmcy_id = $lmcy_id, user_name = '$user_name', m_at = ".time()." WHERE user_id = $user_id");
	if (!$rs) die('修改失败！^_^');
}

put_info('成功提交投资意向！');
header('Location:?c=finance&a=pre_invest');
die;
