<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function update_products_ecotax_v133()
{
	global $oldversion;
	if ($oldversion < '1.3.3.0')
	{
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product` SET `ecotax` = \'0\' WHERE 1');
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'order_detail` SET `ecotax` = \'0\' WHERE 1;');
	}
}
