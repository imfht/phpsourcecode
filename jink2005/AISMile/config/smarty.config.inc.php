<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

define('_PS_SMARTY_DIR_', _PS_TOOL_DIR_.'smarty/');

require_once(_PS_SMARTY_DIR_.'Smarty.class.php');

global $smarty;
$smarty = new Smarty();
$smarty->setCompileDir(_PS_CACHE_DIR_.'smarty/compile');
$smarty->setCacheDir(_PS_CACHE_DIR_.'smarty/cache');
$smarty->setConfigDir(_PS_SMARTY_DIR_.'configs');
$smarty->caching = false;
$smarty->force_compile = (Configuration::get('PS_SMARTY_FORCE_COMPILE') == _PS_SMARTY_FORCE_COMPILE_) ? true : false;
$smarty->compile_check = (Configuration::get('PS_SMARTY_FORCE_COMPILE') <= _PS_SMARTY_CHECK_COMPILE_) ? true : false;

// Production mode
$smarty->debugging = false;
$smarty->debugging_ctrl = 'NONE';

if (Configuration::get('PS_SMARTY_CONSOLE') == _PS_SMARTY_CONSOLE_OPEN_BY_URL_)
	$smarty->debugging_ctrl = 'URL';
else if (Configuration::get('PS_SMARTY_CONSOLE') == _PS_SMARTY_CONSOLE_OPEN_)
	$smarty->debugging = true;

/* Use this constant if you want to load smarty without all MileBiz functions */
if (defined('_PS_SMARTY_FAST_LOAD_') && _PS_SMARTY_FAST_LOAD_)
	return;

if (defined('_PS_ADMIN_DIR_'))
	require_once (dirname(__FILE__).'/smartyadmin.config.inc.php');
else
	require_once (dirname(__FILE__).'/smartyfront.config.inc.php');

if (Configuration::get('PS_HTML_THEME_COMPRESSION'))
	$smarty->registerFilter('output', 'smartyMinifyHTML');
if (Configuration::get('PS_JS_HTML_THEME_COMPRESSION'))
	$smarty->registerFilter('output', 'smartyPackJSinHTML');

smartyRegisterFunction($smarty, 'modifier', 'truncate', 'smarty_modifier_truncate');
smartyRegisterFunction($smarty, 'modifier', 'secureReferrer', array('Tools', 'secureReferrer'));

smartyRegisterFunction($smarty, 'function', 't', 'smartyTruncate'); // unused
smartyRegisterFunction($smarty, 'function', 'm', 'smartyMaxWords'); // unused
smartyRegisterFunction($smarty, 'function', 'p', 'smartyShowObject'); // Debug only
smartyRegisterFunction($smarty, 'function', 'd', 'smartyDieObject'); // Debug only
smartyRegisterFunction($smarty, 'function', 'l', 'smartyTranslate', false);
smartyRegisterFunction($smarty, 'function', 'hook', 'smartyHook');
smartyRegisterFunction($smarty, 'function', 'toolsConvertPrice', 'toolsConvertPrice');

smartyRegisterFunction($smarty, 'function', 'dateFormat', array('Tools', 'dateFormat'));
smartyRegisterFunction($smarty, 'function', 'convertPrice', array('Product', 'convertPrice'));
smartyRegisterFunction($smarty, 'function', 'convertPriceWithCurrency', array('Product', 'convertPriceWithCurrency'));
smartyRegisterFunction($smarty, 'function', 'displayWtPrice', array('Product', 'displayWtPrice'));
smartyRegisterFunction($smarty, 'function', 'displayWtPriceWithCurrency', array('Product', 'displayWtPriceWithCurrency'));
smartyRegisterFunction($smarty, 'function', 'displayPrice', array('Tools', 'displayPriceSmarty'));
smartyRegisterFunction($smarty, 'modifier', 'convertAndFormatPrice', array('Product', 'convertAndFormatPrice')); // used twice
smartyRegisterFunction($smarty, 'function', 'getAdminToken', array('Tools', 'getAdminTokenLiteSmarty'));
smartyRegisterFunction($smarty, 'function', 'displayAddressDetail', array('AddressFormat', 'generateAddressSmarty'));

function smartyDieObject($params, &$smarty)
{
	return Tools::d($params['var']);
}

function smartyShowObject($params, &$smarty)
{
	return Tools::p($params['var']);
}

function smartyMaxWords($params, &$smarty)
{
	Tools::displayAsDeprecated();
	$params['s'] = str_replace('...', ' ...', html_entity_decode($params['s'], ENT_QUOTES, 'UTF-8'));
	$words = explode(' ', $params['s']);

	foreach($words AS &$word)
		if(Tools::strlen($word) > $params['n'])
			$word = Tools::substr(trim(chunk_split($word, $params['n']-1, '- ')), 0, -1);

	return implode(' ',  Tools::htmlentitiesUTF8($words));
}

function smartyTruncate($params, &$smarty)
{
	Tools::displayAsDeprecated();
	$text = isset($params['strip']) ? strip_tags($params['text']) : $params['text'];
	$length = $params['length'];
	$sep = isset($params['sep']) ? $params['sep'] : '...';

	if (Tools::strlen($text) > $length + Tools::strlen($sep))
		$text = Tools::substr($text, 0, $length).$sep;

	return (isset($params['encode']) ? Tools::htmlentitiesUTF8($text, ENT_NOQUOTES) : $text);
}

function milebiz_get_strspacelen($string,$length)
{
    $true_len = 0;
    $i = 0;
    $substr = '';
    while($true_len < $length && $i < mb_strlen($string,Smarty::$_CHARSET)){
        $substr = mb_substr($string, $i, 1, Smarty::$_CHARSET);
        $true_len += (strlen($substr) + mb_strlen($substr,'utf-8'))/2;
        ++$i;
    }
    return $i;
}

function smarty_modifier_truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false, $charset = 'UTF-8')
{
	if (!$length)
		return '';
	$oldlength = $length;
    $length = milebiz_get_strspacelen($string, $length);
	$no_change = $oldlength === $length;
	if (Tools::strlen($string) > $length)
	{
		$length -= min($length, Tools::strlen($etc));
		if (!$break_words && !$middle && $no_change){
			$string = preg_replace('/\s+?(\S+)?$/u', '', Tools::substr($string, 0, $length+1, $charset));			
		}
		return !$middle ? Tools::substr($string, 0, $length, $charset).$etc : Tools::substr($string, 0, $length/2, $charset).$etc.Tools::substr($string, -$length/2, $length, $charset);
	}
	else
		return $string;
}

function smarty_modifier_htmlentitiesUTF8($string)
{
		return Tools::htmlentitiesUTF8($string);
}
function smartyMinifyHTML($tpl_output, &$smarty)
{
    $tpl_output = Media::minifyHTML($tpl_output);
    return $tpl_output;
}

function smartyPackJSinHTML($tpl_output, &$smarty)
{
    $tpl_output = Media::packJSinHTML($tpl_output);
    return $tpl_output;
}

function smartyRegisterFunction($smarty, $type, $function, $params, $lazy = true)
{
	if (!in_array($type, array('function', 'modifier')))
		return false;

	// lazy is better if the function is not called on every page
	if ($lazy)
	{
		$lazy_register = SmartyLazyRegister::getInstance();
		$lazy_register->register($params);

		if (is_array($params))
			$params = $params[1];

		// SmartyLazyRegister allows to only load external class when they are needed
		$smarty->registerPlugin($type, $function, array($lazy_register, $params));
	}
	else
		$smarty->registerPlugin($type, $function, $params);
}

function smartyHook($params, &$smarty)
{
	if (!empty($params['h']))
	{
		$id_module = null;
		$hook_params = $params;
		if (!empty($params['mod']))
		{
			$module = Module::getInstanceByName($params['mod']);
			if ($module && $module->id)
				$id_module = $module->id;
			unset($hook_params['mod']);
		}
		unset($hook_params['h']);
		return Hook::exec($params['h'], $hook_params, $id_module);
	}
}

function toolsConvertPrice($params, &$smarty)
{
	return Tools::convertPrice($params['price'], Context::getContext()->currency);
}

/**
 * Used to delay loading of external classes with smarty->register_plugin
 */
class SmartyLazyRegister
{
	protected $registry = array();
	protected static $instance;

	/**
	 * Register a function or method to be dynamically called later
	 * @param $params function name or array(object name, method name)
	 */
	public function register($params)
	{
		if (is_array($params))
			$this->registry[$params[1]] = $params;
		else
			$this->registry[$params] = $params;
	}

	/**
	 * Dynamically call static function or method
	 *
	 * @param $name function name
	 * @param $arguments function argument
	 * @return mixed function return
	 */
	public function __call($name, $arguments)
	{
		$item = $this->registry[$name];

		// case 1: call to static method - case 2 : call to static function
		if (is_array($item[1]))
			return call_user_func_array($item[1].'::'.$item[0], array($arguments[0], &$arguments[1]));
		else
			return call_user_func_array($item, array($arguments[0], &$arguments[1]));
	}

	public static function getInstance()
	{
		if (!self::$instance)
			self::$instance = new SmartyLazyRegister();
		return self::$instance;
	}
}
