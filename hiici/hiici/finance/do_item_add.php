<?php 

require_once('finance/inc/item_status.php'); 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST['kind'])) die;

$kind = intval($_POST['kind']);
$user_id = $auth['id'];
$user_name = $auth['name'];
$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$money = doubleval($_POST['money']);
$money_v = $money;
$revenue = doubleval($_POST['revenue']);
$term = intval($_POST['term']);
$location = filter_var($_POST['location'], FILTER_SANITIZE_STRING);
$use_to = filter_var($_POST['use_to'], FILTER_SANITIZE_STRING);
$item_info = filter_var($_POST['item_info'], FILTER_SANITIZE_STRING);
$use_to_do = filter_var($_POST['use_to_do'], FILTER_SANITIZE_STRING);
$income = filter_var($_POST['income'], FILTER_SANITIZE_STRING);
$company_info = '';
$risk_control = '';
$lmcy_id = intval($_POST['lmcy_id']);
$phone = doubleval($_POST['phone']);

if (empty($name) || empty($money) || empty($revenue) || empty($term) || empty($location) || empty($use_to) || empty($item_info) || empty($use_to_do) || empty($income) || empty($phone)) {
	put_info('表单内容不规范！');
	header('Location:?c=finance&a=item_add&kind='.$kind);
	die;
}

if (100 < $term) {
	put_info('融资期限过长！');
	header('Location:?c=finance&a=item_add&kind='.$kind);
	die;
}

$rs = dt_query("INSERT INTO finance_item (user_id, user_name, kind, name, money, money_v, revenue, term, location, use_to, item_info, use_to_do, income, company_info, risk_control, audited, status, lmcy_id, phone, c_at) 
	VALUES ('$user_id', '$user_name', '$kind', '$name', '$money', '$money_v', '$revenue', '$term', '$location', '$use_to', '$item_info', '$use_to_do', '$income', '$company_info', '$risk_control', 'N', ".FINANCE_ITEM_STATUS_TB.", $lmcy_id, $phone, ".time().")");
if (!$rs) {
	put_info('提交申请失败！');
	header('Location:?c=finance&a=item_add&kind='.$kind);
	die;
}

// 清空cookie
foreach (array_keys($_POST) as $n) {
	setcookie('item_add_'.$n, '', time()-3600, '/');
}

put_info('提交申请成功！');
header('Location:?c=finance&a=item_list');
die;

