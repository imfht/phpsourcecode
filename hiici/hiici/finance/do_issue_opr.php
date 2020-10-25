<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

global $config;
if (!in_array($auth['id'], $config['manager'])) die('用户权限不够!');

if (empty($_GET)) die;

$issue_id = intval($_GET['issue_id']);
$opr = intval($_GET['opr']);

switch ($opr) {
case 1:
	$rs = dt_query("DELETE FROM finance_issue WHERE id = '$issue_id'");
	break;
}
if (!$rs) die('更新数据失败！'); 

die('s0');
