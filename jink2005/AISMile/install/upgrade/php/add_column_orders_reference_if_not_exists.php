<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_column_orders_reference_if_not_exists()
{
	$column = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'orders` LIKE "reference"');
	if (empty($column))
		return Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'orders` ADD COLUMN `reference` varchar(9) AFTER `id_order`');
}
