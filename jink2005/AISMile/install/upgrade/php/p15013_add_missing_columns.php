<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function p15013_add_missing_columns()
{
	$errors = array();
	$db = Db::getInstance();
	$id_module = $db->getValue('SELECT id_module FROM `'._DB_PREFIX_.'module` WHERE name="statssearch"');

	if ($id_module)
	{
		if (!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'statssearch`
			ADD `id_group_shop` INT(10) NOT NULL default "1" AFTER id_statssearch,
			ADD `id_shop` INT(10) NOT NULL default "1" AFTER id_statssearch'))
		{
			$errors[] = $db->getMsgError();
		}
	}
	if (count($errors))
		return array('error' => 1, 'msg' => implode(',', $errors)) ;
}
