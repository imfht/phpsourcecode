<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function ecotax_tax_application_fix()
{
	if (!Db::getInstance()->execute('SELECT `ecotax_tax_rate` FROM `'._DB_PREFIX_.'order_detail` LIMIT 1'))
		return Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'order_detail` ADD `ecotax_tax_rate` DECIMAL(5, 3) NOT NULL AFTER `ecotax`');
	return true;
}
