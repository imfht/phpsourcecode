<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_missing_shop_column_pagenotfound()
{
	$res = true;
	$exists = Db::getInstance()->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'pagenotfound"');
	if (count($exists))
	{
		$fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'pagenotfound`');
		foreach ($fields as $k => $field)
			$fields[$k] = $field['Field'];

		if (!in_array('id_shop_group', $fields))
			$res &= Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'pagenotfound` 
				ADD `id_shop_group` INT(10) AFTER `id_pagenotfound`');

		if (!in_array('id_shop', $fields))
			$res &= DB::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'pagenotfound` 
				ADD `id_shop` INT(10) AFTER `id_pagenotfound`');
	}
	return $res;
}
