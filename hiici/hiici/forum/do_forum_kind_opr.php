<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

require_once('inc/forum_city.php');

global $config;
if (!in_array($auth['id'], $config['manager']) && dt_query_one("SELECT user_id FROM forum_city_info WHERE id = $forum_city")['user_id'] != $auth['id']) die('用户权限不够!^_^');

if (!empty($_POST)) die;

$opr = intval($_GET['opr']);
$kind_id = intval($_GET['kind_id']);

switch ($opr) {
case 1:
	$rs = dt_query("UPDATE forum SET kind = 0 WHERE kind = '$kind_id'");
	if (!$rs) die('更新forum数据失败！'); 
	$rs = dt_query("DELETE FROM forum_kind WHERE id = '$kind_id'");
	break;
}
if (!$rs) die('更新数据失败！'); 

die('s0');
