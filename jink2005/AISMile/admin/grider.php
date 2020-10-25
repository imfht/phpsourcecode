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
$width = (int)(Tools::getValue('width', 600));
$height = (int)(Tools::getValue('height', 920));
$start = (int)(Tools::getValue('start', 0));
$limit = (int)(Tools::getValue('limit', 40));
$sort = Tools::getValue('sort', 0); // Should be a String. Default value is an Integer because we don't know what can be the name of the column to sort.
$dir = Tools::getValue('dir', 0); // Should be a String : Either ASC or DESC
$id_employee = (int)(Tools::getValue('id_employee'));
$id_lang = (int)(Tools::getValue('id_lang'));


if (!isset($cookie->id_employee) || !$cookie->id_employee  || $cookie->id_employee != $id_employee)
    die(Tools::displayError());

if (!Validate::isModuleName($module))
	die(Tools::displayError());

if (!Tools::file_exists_cache($module_path = dirname(__FILE__).'/../modules/'.$module.'/'.$module.'.php'))
	die(Tools::displayError());

require_once($module_path);

$grid = new $module();
$grid->setEmployee($id_employee);
$grid->setLang($id_lang);
if ($option)
	$grid->setOption($option);
	
$grid->create($render, $type, $width, $height, $start, $limit, $sort, $dir);
$grid->render();


