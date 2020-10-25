<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function set_payment_module_group()
{
	// Get all modules then select only payment ones
	$modules = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'module`');
	foreach ($modules AS $module)
	{
		$file = _PS_MODULE_DIR_.$module['name'].'/'.$module['name'].'.php';
		if (!file_exists($file))
			continue;
		$fd = @fopen($file, 'r');
		if (!$fd)
			continue ;
		$content = fread($fd, filesize($file));
		if (preg_match_all('/extends PaymentModule/U', $content, $matches))
		{
			Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'module_group` (id_module, id_group)
			SELECT '.(int)($module['id_module']).', id_group FROM `'._DB_PREFIX_.'group`');
		}
		fclose($fd);
	}
}

