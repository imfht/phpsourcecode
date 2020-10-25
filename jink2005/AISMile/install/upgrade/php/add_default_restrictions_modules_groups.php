<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_default_restrictions_modules_groups()
{
	$res = true;
	// Table module_group had another use in previous versions, we need to clean it up.
	$res &= Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'module_group`');

	$groups = Db::getInstance()->executeS('
		SELECT `id_group`
		FROM `'._DB_PREFIX_.'group`');
	$modules = Db::getInstance()->executeS('
		SELECT m.*
		FROM `'._DB_PREFIX_.'module` m');
	$shops = Db::getInstance()->executeS('
		SELECT `id_shop`
		FROM `'._DB_PREFIX_.'shop`');
	foreach ($groups as $group)
	{
		if (!is_array($modules))
			return false;
		else
		{
			$sql = 'INSERT INTO `'._DB_PREFIX_.'module_group` (`id_module`, `id_shop`, `id_group`) VALUES ';
			foreach ($modules as $mod)
				foreach ($shops as $s)
					$sql .= '("'.(int)$mod['id_module'].'", "'.(int)$s.'", "'.(int)$group['id_group'].'"),';
			// removing last comma to avoid SQL error
			$sql = substr($sql, 0, strlen($sql) - 1);
			$res &= Db::getInstance()->execute($sql);
		}
	}
	return $res;
}
