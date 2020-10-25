<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function update_carrier_url()
{
	// Get all carriers
	$sql = '
		SELECT c.`id_carrier`, c.`url`
		FROM `'._DB_PREFIX_.'carrier` c';
	$carriers = Db::getInstance()->executeS($sql);

	// Check each one and erase carrier URL if not correct URL
	foreach ($carriers as $carrier)
		if (empty($carrier['url']) || !preg_match('/^https?:\/\/[:#%&_=\(\)\.\? \+\-@\/a-zA-Z0-9]+$/', $carrier['url']))
			Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'carrier`
				SET `url` = \'\'
				WHERE  `id_carrier`= '.(int)($carrier['id_carrier']));
}
