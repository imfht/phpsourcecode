<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */
function updatetabicon_from_11version()
{
	global $oldversion;
	if (version_compare($oldversion,'1.5.0.0','<'))
	{

		$rows = Db::getInstance()->executeS('SELECT `id_tab`,`class_name` FROM '._DB_PREFIX_.'tab');
		if (sizeof($rows))
		{
			$img_dir = scandir(_PS_ROOT_DIR_.'/img/t/');
			$result = true;
			foreach ($rows as $tab)
			{
				if (file_exists(_PS_ROOT_DIR_.'/img/t/'.$tab['id_tab'].'.gif') 
					AND !file_exists(_PS_ROOT_DIR_.'/img/t/'.$tab['class_name'].'.gif'))
					$result &= rename(_PS_ROOT_DIR_.'/img/t/'.$tab['id_tab'].'.gif',_PS_ROOT_DIR_.'/img/t/'.$tab['class_name'].'.gif');
			}
		}
	}
	return true;
}
