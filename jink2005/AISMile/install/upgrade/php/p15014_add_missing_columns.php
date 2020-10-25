<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function p15014_add_missing_columns()
{
	$errors = array();
	$db = Db::getInstance();

	// for module statssearch
	$id_module = $db->getValue('SELECT id_module FROM `'._DB_PREFIX_.'module` WHERE name="statssearch"');
	if ($id_module)
	{
		$list_fields = $db->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'statssearch`');
		foreach($list_fields as $k => $field)
			$list_fields[$k] = $field['Field'];

		if (in_array('id_group_shop', $list_fields))
			if (!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'statssearch`
				CHANGE `id_group_shop` `id_shop_group` INT(10) NOT NULL default "1"'))
			{
				$errors[] = $db->getMsgError();
			}
	}

	if (count($errors))
		return array('error' => 1, 'msg' => implode(',', $errors)) ;
}
