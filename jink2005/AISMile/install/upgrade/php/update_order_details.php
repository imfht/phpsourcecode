<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function update_order_details()
{
	$res = Db::getInstance()->executeS('SHOW COLUMNS FROM `'._DB_PREFIX_.'order_detail` LIKE \'reduction_percent\'');

	if (sizeof($res) == 0)
	{
		  Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'order_detail` ADD `reduction_percent` DECIMAL(10, 2) NOT NULL default \'0.00\' AFTER `product_price`');
		  Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'order_detail` ADD `reduction_amount` DECIMAL(20, 6) NOT NULL default \'0.000000\' AFTER `reduction_percent`');
	}
}


