<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function setAllGroupsOnHomeCategory()
{
	$ps_lang_default = Db::getInstance()->getValue('SELECT value 
		FROM `'._DB_PREFIX_.'`configuration WHERE name="PS_LANG_DEFAULT"');

	$results = Db::getInstance()->executeS('SELECT id_group FROM `'._DB_PREFIX_.'group`');
	$groups = array();
	foreach ($results AS $result)
		$groups[] = $result['id_group'];

	if (is_array($groups) && count($groups))
	{
		// cleanGroups
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_group` 
			WHERE `id_category` = 1');
		// addGroups($groups);
		$row = array('id_category' => 1, 'id_group' => (int)$groups);
		Db::getInstance()->insert('category_group', $row);
	}
}
