<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_order_state($conf_name, $name, $invoice, $send_email, $color, $unremovable, $logable, $delivery, $template = null)
{
	$res = true;
	$name_lang = array();
	$template_lang = array();
	foreach (explode('|', $name) AS $item)
	{
		$temp = explode(':', $item);
		$name_lang[$temp[0]] = $temp[1];
	}
	
	if ($template)
		foreach (explode('|', $template) AS $item)
		{
			$temp = explode(':', $item);
			$template_lang[$temp[0]] = $temp[1];
		}
	
	$res &= Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'order_state` (`invoice`, `send_email`, `color`, `unremovable`, `logable`, `delivery`) 
		VALUES ('.(int)$invoice.', '.(int)$send_email.', "'.$color.'", '.(int)$unremovable.', '.(int)$logable.', '.(int)$delivery.')');
	
	$id_order_state = Db::getInstance()->getValue('
		SELECT MAX(`id_order_state`)
		FROM `'._DB_PREFIX_.'order_state`');
	
	$languages = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'lang`');
	foreach ($languages AS $lang)
	{
		$iso_code = $lang['iso_code'];
		$iso_code_name = isset($name_lang[$iso_code])?$iso_code:'zh';
		$iso_code_template = isset($template_lang[$iso_code])?$iso_code:'zh';
		$name = isset($name_lang[$iso_code]) ? $name_lang[$iso_code] : $name_lang['zh'];
		$template = isset($template_lang[$iso_code]) ? $template_lang[$iso_code] : '';

		$res &= Db::getInstance()->execute('
		INSERT IGNORE INTO `'._DB_PREFIX_.'order_state_lang` (`id_lang`, `id_order_state`, `name`, `template`) 
		VALUES ('.(int)$lang['id_lang'].', '.(int)$id_order_state.', "'. $name .'", "'. $template .'")
		');
	}
	
	$res &= Db::getInstance()->getValue('REPLACE INTO `'._DB_PREFIX_.'configuration`
		(name, value) VALUES ("'.$conf_name.'", "'.$id_order_state.'"');
}
