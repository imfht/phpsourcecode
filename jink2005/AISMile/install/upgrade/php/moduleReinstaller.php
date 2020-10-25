<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function moduleReinstaller($moduleName, $force = false)
{
	$module = Module::getInstanceByName($moduleName);
	if (!is_object($module))
		die(Tools::displayError());
	if ($module->uninstall() OR $force)
		return $module->install();
	return false;
}

