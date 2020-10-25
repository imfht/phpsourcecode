<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (!empty($_POST)) die;

$item_id = intval($_GET['item_id']);

global $config;
if (!in_array($auth['id'], $config['manager'])) if (1 > dt_count('finance_item', 'WHERE id = '.$item_id.' AND lmcy_id = (SELECT id FROM finance_lmcy WHERE user_id = '.$auth['id'].')')) die('用户权限不够!');

$opr = intval($_GET['opr']);

switch ($opr) {
case 1:
	$rs = dt_query("UPDATE finance_item SET audited = 'Y' WHERE id = '$item_id'");
	break;
case 2:
	$rs = dt_query("UPDATE finance_item SET audited = 'N' WHERE id = '$item_id'");
	break;
case 3:
	$rs = dt_query("DELETE FROM finance_item WHERE id = '$item_id'");
	do_rmdir('img/finance/item/'.$item_id);
	break;
case 4:
	$htxy = 'img/finance/item/'.$item_id.'/htxy/'.filter_var($_GET['htxy'], FILTER_SANITIZE_STRING);
	if (!file_exists($htxy)) die('合同协议不存在！');
	$rs = unlink($htxy);
	break;
case 5:
	$sdzp = 'img/finance/item/'.$item_id.'/sdzp/'.filter_var($_GET['sdzp'], FILTER_SANITIZE_STRING);
	if (!file_exists($sdzp)) die('实地照片不存在！');
	$rs = unlink($sdzp);
	break;
}
if (!$rs) die('更新数据失败！'); 

die('s0');
