<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

global $config;
if (!in_array($auth['id'], $config['manager'])) die('用户权限不够!');

if (empty($_GET)) die;

$lmcy_id = intval($_GET['lmcy_id']);
$opr = intval($_GET['opr']);

switch ($opr) {
case 1:
	//删除成员图标
	$logo_f = 'img/finance/lmcy/logo/'.$lmcy_id;
	if (file_exists($logo_f)) if (!unlink($logo_f)) die('图标删除失败！^_^');
	//删除成员头图
	$h_img_f = 'img/finance/lmcy/h_img/'.$lmcy_id;
	if (file_exists($h_img_f)) if (!unlink($h_img_f)) die('图标删除失败！^_^');

	$rs = dt_query("DELETE FROM finance_lmcy WHERE id = '$lmcy_id'");
	break;
}
if (!$rs) die('更新数据失败！'); 

die('s0');
