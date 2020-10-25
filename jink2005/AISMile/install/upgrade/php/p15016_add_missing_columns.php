<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function p15016_add_missing_columns()
{
	$errors = array();

	$id_module = Db::getInstance()->getValue('SELECT id_module FROM `'._DB_PREFIX_.'module` WHERE name="blockreinsurance"');
	if ($id_module)
	{
		$list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'reinsurance`');
		foreach ($list_fields as $k => $field)
			$list_fields[$k] = $field['Field'];

		if (!in_array('id_shop', $list_fields))
			if (!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'reinsurance` ADD `id_shop` INT(10) NOT NULL default "1" AFTER id_reinsurance'))
				$errors[] = Db::getInstance()->getMsgError();
	}
	
	$id_module = Db::getInstance()->getValue('SELECT id_module FROM `'._DB_PREFIX_.'module` WHERE name="blocktopmenu"');
	if ($id_module)
	{
		$list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'linksmenutop`');
		foreach ($list_fields as $k => $field)
			$list_fields[$k] = $field['Field'];

		if (in_array('id_link', $list_fields) && !in_array('id_linksmenutop', $list_fields))
			if (!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'linksmenutop` CHANGE `id_link` `id_linksmenutop` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT'))
				$errors[] = Db::getInstance()->getMsgError();
				
		$list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'linksmenutop_lang`');
		foreach ($list_fields as $k => $field)
			$list_fields[$k] = $field['Field'];

		if (in_array('id_link', $list_fields) && !in_array('id_linksmenutop', $list_fields))
			if (!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'linksmenutop_lang` CHANGE `id_link` `id_linksmenutop` INT(10) UNSIGNED NOT NULL'))
				$errors[] = Db::getInstance()->getMsgError();
	}

	if (count($errors))
		return array('error' => 1, 'msg' => implode(',', $errors)) ;
}