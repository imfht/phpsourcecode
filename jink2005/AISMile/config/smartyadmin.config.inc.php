<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */
global $smarty;
$smarty->debugging = false;
$smarty->debugging_ctrl = 'NONE';

function smartyTranslate($params, &$smarty)
{
	$htmlentities = !isset($params['js']);
	$pdf = isset($params['pdf']);
	$addslashes = isset($params['slashes']);
	$sprintf = isset($params['sprintf']) ? $params['sprintf'] : false;

	if ($pdf)
		return Translate::getPdfTranslation($params['s']);

	$filename = ((!isset($smarty->compiler_object) || !is_object($smarty->compiler_object->template)) ? $smarty->template_resource : $smarty->compiler_object->template->getTemplateFilepath());

	// If the template is part of a module
	if (!empty($params['mod']))
		return Translate::getModuleTranslation($params['mod'], $params['s'], basename($filename, '.tpl'), $sprintf);

	// If the tpl is at the root of the template folder
	if (dirname($filename) == '.')
		$class = 'index';
	// If the tpl is used by a Helper
	elseif (strpos($filename, 'helpers') === 0)
		$class = 'Helper';
	// If the tpl is used by a Controller
	else
	{
		// Split by \ and / to get the folder tree for the file
		$folder_tree = preg_split('#[/\\\]#', $filename);
		$key = array_search('controllers', $folder_tree);

		// If there was a match, construct the class name using the child folder name
		// Eg. xxx/controllers/customers/xxx => AdminCustomers
		if ($key !== false)
			$class = 'Admin'.Tools::toCamelCase($folder_tree[$key + 1], true);
		else
			$class = null;
	}

	return Translate::getAdminTranslation($params['s'], $class, $addslashes, $htmlentities, $sprintf);
}

