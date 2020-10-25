<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_carrier_position()
{
	$carriers = Db::getInstance()->executeS('
	SELECT `id_carrier`
	FROM `'._DB_PREFIX_.'carrier`
	WHERE `deleted` = 0');
	if (count($carriers) && is_array($carriers))
	{
		$i = 0;
		foreach ($carriers as $carrier)
		{
			Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'carrier` 
			SET `position` = '.$i++.'
			WHERE `id_carrier` = '.(int)$carrier['id_carrier']);
		}
	}
}