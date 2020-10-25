<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

require_once 'init.php';

try
{
	require_once _PS_INSTALL_PATH_.'classes/controllerHttp.php';
	InstallControllerHttp::execute();
}
catch (MileBizInstallerException $e)
{
	$e->displayMessage();
}
