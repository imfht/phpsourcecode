<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_unknown_gender()
{
	$res = true;

	// creates the new gender
	$id_type = 2;
	$res &= Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'gender` (`type`)
		VALUES ('.(int)$id_type.')');

	// retrieves its id
	$id_gender = Db::getInstance()->Insert_ID();

	// inserts lang values
	$languages = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'lang`');
	$lang_names = array(
		1 => 'Unknown',
		2 => 'Unbekannte',
		3 => 'Desconocido',
		4 => 'Inconnu',
		5 => 'Sconosciuto',
	);

	foreach ($languages as $lang)
	{
		$name = (isset($lang_names[$lang['id_lang']]) ? $lang_names[$lang['id_lang']] : 'Unknown');
		$res &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'gender_lang` (`id_gender`, `id_lang`, `name`) VALUES
				('.(int)$id_gender.', '.(int)$lang['id_lang'].', \''.pSQL($name).'\')');
	}

	// for all clients where id gender is 0, sets the new id gender
	$res &= Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'customers`
		SET `id_gender` = '.(int)$id_gender.'
		WHERE `id_gender` = 0');
}
