<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

require_once(dirname(__FILE__).'/../config/config.inc.php');
require_once(dirname(__FILE__).'/init.php');

if (isset($_GET['img']) AND Validate::isMd5($_GET['img']) AND isset($_GET['name']) AND Validate::isGenericName($_GET['name']) AND file_exists(_PS_UPLOAD_DIR_.$_GET['img']))
{
	header('Content-type: image/jpeg');
	header('Content-Disposition: attachment; filename="'.$_GET['name'].'.jpg"');
	echo file_get_contents(_PS_UPLOAD_DIR_.$_GET['img']);
}