<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/**
 * This function copy all images located in /install/data/img/* that are missing in previous upgrade
 *  in the matching img dir. This does not modify images that are already present.
 *  
 */
function p15014_copy_missing_images_tab_from_installer()
{
	$res = true;
	$DIR_SEP = DIRECTORY_SEPARATOR;
	if (!defined('_PS_ROOT_DIR_'))
		define('_PS_ROOT_DIR_', realpath(INSTALL_PATH.'/../'));

	$install_dir_path = INSTALL_PATH.$DIR_SEP.'data'.$DIR_SEP.'img';
	$img_dir = scandir($install_dir_path);
	foreach($img_dir as $dir)
	{
		if ($dir[0] == '.' || !is_dir($install_dir_path.$DIR_SEP.$dir))
			continue;

		$img_subdir = scandir($install_dir_path.$DIR_SEP.$dir);
		foreach($img_subdir as $img)
		{
			if ($img[0] == '.')
				continue;
			if (!file_exists(_PS_ROOT_DIR_.$DIR_SEP.'img'.$DIR_SEP.$dir.$DIR_SEP.$img))
				$res &= copy($install_dir_path.$DIR_SEP.$dir.$DIR_SEP.$img, _PS_ROOT_DIR_.$DIR_SEP.'img'.$DIR_SEP.$dir.$DIR_SEP.$img);
		}
	}

	return $res;
}

