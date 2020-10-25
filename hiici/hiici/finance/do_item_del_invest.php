<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET)) die;

$item_id = intval($_GET['item_id']);
$user_id = $auth['id'];

$cond = 'WHERE item_id = '.$item_id.' AND user_id = '.$user_id;

if (1 > dt_count('finance_item_invest', $cond)) die('违规操作！没有您的数据');

$rs = dt_query("DELETE FROM finance_item_invest $cond"); 
if (!$rs) die('数据变更失败!');

$rs = dt_query("UPDATE finance_item SET invest = invest - 1 WHERE id = $item_id");
if (!$rs) die('更新意向投资人数失败！');

die('s0');

