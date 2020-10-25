<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_column_order_state_deleted_if_not_exists()
{
	$res  = true;
	$column = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'order_state` LIKE "deleted"');

	if (empty($column))
		$res = Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'order_state` 
			ADD COLUMN `deleted` tinyint(1) UNSIGNED NOT NULL default "0" AFTER `paid`');
	if (!$res)
		return array('error' => Db::getInstance()->getNumberError(), 'msg' => Db::getInstance()->getMsgError());
	return true;
}
