<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function p15010_drop_column_id_address_if_exists()
{
	$res = true;
	$exists = Db::getInstance()->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'supplier"');
	if (count($exists))
	{
		$fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'supplier`');
		foreach ($fields as $k => $field)
			$fields[$k] = $field['Field'];

		if (in_array('id_address', $fields))
			$res &= Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'supplier` 
				DROP `id_address`');
	}
	return $res;
}
