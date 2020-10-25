<?php

/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */
function upgrade_cms_15_rename()
{
	$res = true;
	$db = Db::getInstance();

	$res &= $db->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'_cms_shop"');
	if ($res)
	{
		$res &= $db->execute('RENAME TABLE `'._DB_PREFIX_.'_cms_shop` to `'._DB_PREFIX_.'cms_shop`');
		// in case the script upgrade_cms_15.php have set a wrong table name, it's empty
		$res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cms_shop` (id_shop, id_cms) 
			(SELECT 1, id_cms FROM '._DB_PREFIX_.'cms)');

		// cms_block table is blockcms module dependant. Don't update table that does not exists
		$table_cms_block_exists = $db->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'cms"');
		if (!$table_cms_block_exists)
			return $res;
		$res &= Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'cms` 
			ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT "1" AFTER `id_cms`');
	}

	return $res;
}
