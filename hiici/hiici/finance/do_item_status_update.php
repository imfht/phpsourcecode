<?php

require_once('finance/inc/item_status.php');

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

global $config;
if (!in_array($auth['id'], $config['manager'])) die('用户权限不够!');

$items = dt_query("SELECT id, term, c_at FROM finance_item WHERE status < ".FINANCE_ITEM_STATUS_JS);
if (!$items) {
	put_info('获取数据失败！');
	header('Location:?c=finance&a=item_manage');
	die;
}

while($item = mysql_fetch_array($items)) {
	$t_n = time() - $item['c_at'];
	if ($t_n > ($item['term']+1)*31*24*3600) { 
		dt_query("UPDATE finance_item SET status = ".FINANCE_ITEM_STATUS_JS." WHERE id = ".$item['id']);
	} elseif ($t_n > 31*24*3600) {
		dt_query("UPDATE finance_item SET status = ".FINANCE_ITEM_STATUS_HK." WHERE id = ".$item['id']);
	}
}

put_info('成功更新状态！');
header('Location:?c=finance&a=item_manage');

die;
