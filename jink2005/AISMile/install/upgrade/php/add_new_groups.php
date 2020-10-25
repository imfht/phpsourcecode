<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_new_groups($french, $standard)
{
	$res = Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'group` (`id_group`, `date_add`, `date_upd`) VALUES (NULL, NOW(), NOW())');
	$last_id = Db::getInstance()->Insert_ID();
	
	$languages = Db::getInstance()->executeS('SELECT id_lang, iso_code FROM `'._DB_PREFIX_.'lang`');

	$sql = '';
	foreach ($languages as $lang)
		if (strtolower($lang['iso_code']) == 'fr')
			$sql .= '('.(int)$last_id.', '.(int)$lang['id_lang'].', "'.pSQL($french).'"),';
		else
			$sql .= '('.(int)$last_id.', '.(int)$lang['id_lang'].', "'.pSQL($standard).'"),';
	$sql = substr($sql, 0, strlen($sql) - 1);
	$res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'group_lang` (`id_group`, `id_lang`, `name`) VALUES '.$sql);
	// we add the different id_group in the configuration
	if (strtolower($standard) == 'visitor')
		$res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'configuration` (`id_configuration`, `name`, `value`, `date_add`, `date_upd`) VALUES (NULL, "PS_UNIDENTIFIED_GROUP", "'.(int)$last_id.'", NOW(), NOW())');
	else if (strtolower($standard) == 'guest')
		$res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'configuration` (`id_configuration`, `name`, `value`, `date_add`, `date_upd`) VALUES (NULL, "PS_GUEST_GROUP", "'.(int)$last_id.'", NOW(), NOW())');
	else if (strtolower($standard) == 'test')
		$res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'configuration` (`id_configuration`, `name`, `value`, `date_add`, `date_upd`) VALUES (NULL, "PS_TEST", "'.(int)$last_id.'", NOW(), NOW())');

	// Add shop association
	$res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'group_shop` (`id_group`, `id_shop`) (SELECT '.(int)$last_id.', `value` FROM `'._DB_PREFIX_.'configuration` WHERE `name` = \'PS_SHOP_DEFAULT\')');

	// Copy categories associations from the group of id 1 (default group for both visitors and customers in version 1.4) to the new group
	$res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'category_group` (`id_category`, `id_group`) (SELECT `id_category`, '.(int)$last_id.' FROM `'._DB_PREFIX_.'category_group` WHERE `id_group` = 1)');

	return $res;
}
