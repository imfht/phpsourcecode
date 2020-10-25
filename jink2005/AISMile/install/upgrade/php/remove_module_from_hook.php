<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function remove_module_from_hook($module_name, $hook_name)
{
	$result = true;

	$id_module = Db::getInstance()->getValue('
	SELECT `id_module` FROM `'._DB_PREFIX_.'module`
	WHERE `name` = \''.pSQL($module_name).'\''
	);

	if ((int)$id_module > 0)
	{
		$id_hook = Db::getInstance()->getValue('
		SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \''.pSQL($hook_name).'\'
		');

		if ((int)$id_hook > 0)
		{
			$result &= Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'hook_module`
			WHERE `id_module` = '.(int)$id_module.' AND `id_hook` = '.(int)$id_hook);
		}
	}
	
	return $result;
}

