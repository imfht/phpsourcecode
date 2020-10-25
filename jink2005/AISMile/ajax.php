<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

require(dirname(__FILE__).'/config/config.inc.php');

if (Tools::isSubmit('ajaxStates') AND Tools::isSubmit('id_country'))
{
	$states = Db::getInstance()->executeS('
	SELECT s.id_state, s.name
	FROM '._DB_PREFIX_.'state s
	LEFT JOIN '._DB_PREFIX_.'country c ON (s.`id_country` = c.`id_country`)
	WHERE s.id_country = '.(int)(Tools::getValue('id_country')).' AND s.active = 1 AND c.`contains_states` = 1
	ORDER BY s.`name` ASC');

	if (is_array($states) AND !empty($states))
	{
		$list = '';
		if (Tools::getValue('no_empty') != true)
		{
			$empty_value = (Tools::isSubmit('empty_value')) ? Tools::getValue('empty_value') : '----------';
			$list = '<option value="0">'.Tools::htmlentitiesUTF8($empty_value).'</option>'."\n";
		}

		foreach ($states AS $state)
			$list .= '<option value="'.(int)($state['id_state']).'"'.((isset($_GET['id_state']) AND $_GET['id_state'] == $state['id_state']) ? ' selected="selected"' : '').'>'.$state['name'].'</option>'."\n";
	}
	else
		$list = 'false';

	die($list);
}

if (isset($_GET['ajaxCities']) AND isset($_GET['id_state']))
{
	$cities = City::getCitysByIdState($_GET['id_state']);
	if (is_array($cities) AND !empty($cities))
	{
		$list = '';
		if (Tools::getValue('no_empty') != true)
			$list = '<option value="0">-----------</option>'."\n";

		foreach ($cities AS $city)
			$list .= '<option value="'.(int)($city['id_city']).'"'.((isset($_GET['id_city']) AND $_GET['id_city'] == $city['id_city']) ? ' selected="selected"' : '').'>'.$city['name'].'</option>'."\n";
	}
	else
		$list = 'false';

	die($list);
}
