<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST['item_id'])) die;

$item_id = intval($_POST['item_id']);

global $config;
if (!in_array($auth['id'], $config['manager'])) if (1 > dt_count('finance_item', 'WHERE id = '.$item_id.' AND lmcy_id = (SELECT id FROM finance_lmcy WHERE user_id = '.$auth['id'].')')) die('用户权限不够!');

$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$money = doubleval($_POST['money']);
$money_v = doubleval($_POST['money_v']);
$revenue = doubleval($_POST['revenue']);
$term = intval($_POST['term']);
$location = filter_var($_POST['location'], FILTER_SANITIZE_STRING);
$use_to = filter_var($_POST['use_to'], FILTER_SANITIZE_STRING);
$item_info = filter_var($_POST['item_info'], FILTER_SANITIZE_STRING);
$use_to_do = filter_var($_POST['use_to_do'], FILTER_SANITIZE_STRING);
$income = filter_var($_POST['income'], FILTER_SANITIZE_STRING);
$company_info = cleanjs($_POST['company_info']);
$risk_control = cleanjs($_POST['risk_control']);
$credit_s = intval($_POST['credit_s']);
$financial_s = intval($_POST['financial_s']);
$operate_s = intval($_POST['operate_s']);
$lmcy_id = intval($_POST['lmcy_id']);
$phone = doubleval($_POST['phone']);

if (empty($name) || empty($money) || empty($revenue) || empty($term) || empty($location) || empty($use_to) || empty($item_info) || empty($use_to_do) || empty($income)) {
	put_info('表单内容不规范！');
	header('Location:?c=finance&a=item_edit&item_id='.$item_id);
	die;
}

if (100 < $term) {
	put_info('融资期限过长！');
	header('Location:?c=finance&a=item_add');
	die;
}

$rs = dt_query("UPDATE finance_item SET name='$name', money='$money', money_v='$money_v', revenue='$revenue', term='$term', location='$location', use_to='$use_to', item_info='$item_info', use_to_do='$use_to_do', income='$income', company_info='$company_info', risk_control='$risk_control', credit_s='$credit_s', financial_s='$financial_s', operate_s='$operate_s', lmcy_id='$lmcy_id', phone='$phone' WHERE id=$item_id");
if (!$rs) {
	put_info('更新项目失败！');
	header('Location:?c=finance&a=item_edit&item_id='.$item_id);
	die;
}

//上传相关资料【危险】
$c = 0;
foreach ($_FILES as $k => $f) {
	if (30 < $c) break;  //上传数量限制
	if (empty($f['name'])) continue;
	if (strstr($k, 'htxy')) {
		if (file_exists('img/finance/item/'.$item_id.'/htxy/'.$f['name'])) continue;
		if (!do_upload_file($f, 'img/finance/item/'.$item_id.'/htxy/', array('image/gif', 'image/jpeg', 'image/pjpeg'))) die('合同协议上传失败!');
	} elseif (strstr($k, 'sdzp')) {
		if (file_exists('img/finance/item/'.$item_id.'/sdzp/'.$f['name'])) continue;
		if (!do_upload_file($f, 'img/finance/item/'.$item_id.'/sdzp/', array('image/gif', 'image/jpeg', 'image/pjpeg'))) die('实地照片上传失败!');
	}
	$c += 1;
}



put_info('项目更新成功！');
header('Location:?c=finance&a=item_manage');
die;

