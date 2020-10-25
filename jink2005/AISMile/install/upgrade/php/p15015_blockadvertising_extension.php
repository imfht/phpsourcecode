<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function p15015_blockadvertising_extension()
{
	if (!defined('_PS_ROOT_DIR_'))
		define('_PS_ROOT_DIR_', realpath(INSTALL_PATH.'/../'));

	// Try to update with the extension of the image that exists in the module directory
	if (file_exists(_PS_ROOT_DIR_.'modules/blockadvertising'))
		foreach (scandir(_PS_ROOT_DIR_.'modules/blockadvertising') as $file)
			if (in_array($file, array('advertising.jpg', 'advertising.gif', 'advertising.png')))
				Db::getInstance()->execute('
				REPLACE INTO `'._DB_PREFIX_.'configuration` (name, value)
				VALUES ("BLOCKADVERT_IMG_EXT", "'.pSQL(substr($file, strrpos($file, '.') + 1)).'"');
	return true;
}