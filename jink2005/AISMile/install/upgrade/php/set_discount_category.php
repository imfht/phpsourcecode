<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function set_discount_category()
{
	$discounts = Db::getInstance()->executeS('SELECT `id_discount` FROM `'._DB_PREFIX_.'discount`');
	$categories = Db::getInstance()->executeS('SELECT `id_category` FROM `'._DB_PREFIX_.'category`');
	foreach ($discounts AS $discount)
		foreach ($categories AS $category)
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'discount_category` (`id_discount`,`id_category`) VALUES ('.(int)($discount['id_discount']).','.(int)($category['id_category']).')');
}
