<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

define('_PS_ADMIN_DIR_', getcwd());
require(dirname(__FILE__).'/../config/config.inc.php');
require(dirname(__FILE__).'/functions.php');

// For retrocompatibility with "tab" parameter
if (!isset($_GET['controller']) && isset($_GET['tab']))
	$_GET['controller'] = strtolower($_GET['tab']);
if (!isset($_POST['controller']) && isset($_POST['tab']))
	$_POST['controller'] = strtolower($_POST['tab']);
if (!isset($_REQUEST['controller']) && isset($_REQUEST['tab']))
	$_REQUEST['controller'] = strtolower($_REQUEST['tab']);
// Retrocompatibility with 1.4
$_REQUEST['ajaxMode'] = $_POST['ajaxMode'] = $_GET['ajaxMode'] = $_REQUEST['ajax'] = $_POST['ajax'] = $_GET['ajax'] = 1;

Dispatcher::getInstance()->dispatch();