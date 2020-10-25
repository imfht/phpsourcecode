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
include_once(dirname(__FILE__).'/../config/config.inc.php');

$module = Tools::getValue('module');
$render = Tools::getValue('render');
$type = Tools::getValue('type');
$option = Tools::getValue('option');
$layers = Tools::getValue('layers');
$width = Tools::getValue('width');
$height = Tools::getValue('height');
$id_employee = Tools::getValue('id_employee');
$id_lang = Tools::getValue('id_lang');


if (!isset($cookie->id_employee) || !$cookie->id_employee  || $cookie->id_employee != $id_employee)
    die(Tools::displayError());
    
if (!Validate::isModuleName($module))
	die(Tools::displayError());

if (!Tools::file_exists_cache($module_path = dirname(__FILE__).'/../modules/'.$module.'/'.$module.'.php'))
	die(Tools::displayError());

require_once($module_path);

$graph = new $module();
$graph->setEmployee($id_employee);
$graph->setLang($id_lang);
if ($option)
	$graph->setOption($option, $layers);

$graph->create($render, $type, $width, $height, $layers);
$graph->draw();

