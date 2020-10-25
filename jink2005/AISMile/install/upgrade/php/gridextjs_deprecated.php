<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/** remove the uncompatible module gridextjs (1.4.0.8 upgrade)
 */
function gridextjs_deprecated()
{
	// if exists, use _PS_MODULE_DIR_ or _PS_ROOT_DIR_
	// instead of guessing the modules dir
	if (defined('_PS_MODULE_DIR_'))
		$gridextjs_path = _PS_MODULE_DIR_ . 'gridextjs';
	else
		if (defined('_PS_ROOT_DIR_'))
			$gridextjs_path = _PS_ROOT_DIR_ . '/modules/gridextjs';
		else
			$gridextjs_path = dirname(__FILE__).'/../../modules/gridextjs';

	if (file_exists($gridextjs_path))
		return rename($gridextjs_path, str_replace('gridextjs', 'gridextjs.deprecated', $gridextjs_path));

	return true;
}

