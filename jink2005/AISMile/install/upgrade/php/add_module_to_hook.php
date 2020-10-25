<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_module_to_hook($module_name, $hook_name)
{
	$res = true;

	$id_module = Db::getInstance()->getValue('
	SELECT `id_module` FROM `'._DB_PREFIX_.'module`
	WHERE `name` = "'.$module_name.'"'
	);

	if ((int)$id_module > 0)
	{
		$id_hook = Db::getInstance()->getValue('
		SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = "'.$hook_name.'"
		');

		if ((int)$id_hook > 0)
		{
			$res &= Db::getInstance()->execute('
			INSERT IGNORE INTO `'._DB_PREFIX_.'hook_module` (`id_module`, `id_hook`, `position`)
			VALUES (
			'.(int)$id_module.',
			'.(int)$id_hook.',
			(SELECT IFNULL(
				(SELECT max_position from (SELECT MAX(position)+1 as max_position  FROM `'._DB_PREFIX_.'hook_module`  WHERE `id_hook` = '.(int)$id_hook.') AS max_position), 1))
			)');
		}
	}

	return $res;
}

