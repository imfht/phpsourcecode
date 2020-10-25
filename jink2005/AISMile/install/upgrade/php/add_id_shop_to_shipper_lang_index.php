<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_id_shop_to_shipper_lang_index()
{
	$res = true;
	
	$key_exists = Db::getInstance()->executeS('
	SHOW INDEX
	FROM `'._DB_PREFIX_.'carrier_lang`
	WHERE Key_name = "shipper_lang_index"');
	if ($key_exists)
		$res &= Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'carrier_lang` DROP KEY `shipper_lang_index`');
	$res &= Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'carrier_lang` ADD PRIMARY KEY (`id_carrier`, `id_shop`, `id_lang`)');

	return $res;
}
