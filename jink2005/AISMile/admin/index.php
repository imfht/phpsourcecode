<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

$timer_start = microtime(true);
define('_PS_ADMIN_DIR_', getcwd());

require(dirname(__FILE__).'/../config/config.inc.php');
require(dirname(__FILE__).'/functions.php');

//small test to clear cache after upgrade
if (Configuration::get('PS_UPGRADE_CLEAR_CACHE'))
{
	header('Cache-Control: max-age=0, must-revalidate');
	header('Expires: Mon, 06 Jun 1985 06:06:00 GMT+1');
	Configuration::updateValue('PS_UPGRADE_CLEAR_CACHE', 0);
}

// For retrocompatibility with "tab" parameter
if (!isset($_GET['controller']) && isset($_GET['tab']))
	$_GET['controller'] = strtolower($_GET['tab']);
if (!isset($_POST['controller']) && isset($_POST['tab']))
	$_POST['controller'] = strtolower($_POST['tab']);
if (!isset($_REQUEST['controller']) && isset($_REQUEST['tab']))
	$_REQUEST['controller'] = strtolower($_REQUEST['tab']);

// Prepare and trigger admin dispatcher
Dispatcher::getInstance()->dispatch();