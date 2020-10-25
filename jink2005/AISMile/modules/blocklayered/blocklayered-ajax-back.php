<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/* Getting cookie or logout */
include(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

if (substr(Tools::encrypt('blocklayered/index'),0,10) != Tools::getValue('layered_token') || !Module::isInstalled('blocklayered'))
	die('Bad token');

include(dirname(__FILE__).'/blocklayered.php');

$category_box = Tools::getValue('categoryBox');

/* Clean categoryBox before use */
if (is_array($category_box))
	foreach ($category_box AS &$value)
		$value = (int)$value;

$blockLayered = new BlockLayered();
echo $blockLayered->ajaxCallBackOffice($category_box, Tools::getValue('id_layered_filter'));
