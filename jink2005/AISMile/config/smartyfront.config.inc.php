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
$smarty->setTemplateDir(_PS_THEME_DIR_.'tpl');

function smartyTranslate($params, &$smarty)
{
	global $_LANG;

	if (!isset($params['js'])) $params['js'] = 0;
	if (!isset($params['pdf'])) $params['pdf'] = false;
	if (!isset($params['mod'])) $params['mod'] = false;
	if (!isset($params['sprintf'])) $params['sprintf'] = null;

	$string = str_replace('\'', '\\\'', $params['s']);
	$filename = ((!isset($smarty->compiler_object) || !is_object($smarty->compiler_object->template)) ? $smarty->template_resource : $smarty->compiler_object->template->getTemplateFilepath());

	$basename = basename($filename, '.tpl');
	$key = $basename.'_'.md5($string);

	if (isset($smarty->source) && (strpos($smarty->source->filepath, DIRECTORY_SEPARATOR.'override'.DIRECTORY_SEPARATOR) !== false))
		$key = 'override_'.$key;

	if ($params['mod'])
		return Translate::getModuleTranslation($params['mod'], $params['s'], $basename, $params['sprintf']);
	else if ($params['pdf'])
		return Translate::getPdfTranslation($params['s']);

	if ($_LANG != null && isset($_LANG[$key]))
		$msg = $_LANG[$key];
	elseif ($_LANG != null && isset($_LANG[Tools::strtolower($key)]))
		$msg = $_LANG[Tools::strtolower($key)];
	else
		$msg = $params['s'];

	if ($msg != $params['s'])
		$msg = $params['js'] ? addslashes($msg) : stripslashes($msg);

	if ($params['sprintf'] !== null)
		$msg = Translate::checkAndReplaceArgs($msg, $params['sprintf']);

	return $params['js'] ? $msg : Tools::htmlentitiesUTF8($msg);
}

