<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_new_tab($className, $name, $id_parent, $returnId = false)
{
	$array = array();
	foreach (explode('|', $name) AS $item)
	{
		$temp = explode(':', $item);
		$array[$temp[0]] = $temp[1];
	}
	
	if (!(int)Db::getInstance()->getValue('SELECT count(id_tab) FROM `'._DB_PREFIX_.'tab` WHERE `class_name` = \''.pSQL($className).'\' '))
		Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'tab` (`id_parent`, `class_name`, `module`, `position`) VALUES ('.(int)$id_parent.', \''.pSQL($className).'\', \'\', 
									(SELECT IFNULL(MAX(t.position),0)+ 1 FROM `'._DB_PREFIX_.'tab` t WHERE t.id_parent = '.(int)$id_parent.'))');
	
	$languages = Db::getInstance()->executeS('SELECT id_lang, iso_code FROM `'._DB_PREFIX_.'lang`');
	foreach ($languages AS $lang)
	{
		Db::getInstance()->execute('
		INSERT IGNORE INTO `'._DB_PREFIX_.'tab_lang` (`id_lang`, `id_tab`, `name`) 
		VALUES ('.(int)$lang['id_lang'].', (
				SELECT `id_tab`
				FROM `'._DB_PREFIX_.'tab`
				WHERE `class_name` = \''.pSQL($className).'\' LIMIT 0,1
			), \''.pSQL(isset($array[$lang['iso_code']]) ? $array[$lang['iso_code']] : $array['en']).'\')
		');
	}
								
	Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) 
								(SELECT `id_profile`, (
								SELECT `id_tab`
								FROM `'._DB_PREFIX_.'tab`
								WHERE `class_name` = \''.pSQL($className).'\' LIMIT 0,1
								), 1, 1, 1, 1 FROM `'._DB_PREFIX_.'profile` )');

	if($returnId) {
		return (int)Db::getInstance()->getValue('SELECT `id_tab`
								FROM `'._DB_PREFIX_.'tab`
								WHERE `class_name` = \''.pSQL($className).'\'');
	}
}
