<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function id_currency_country_fix()
{
	if (!Db::getInstance()->execute('SELECT `id_currency` FROM `'._DB_PREFIX_.'country` LIMIT 1'))
		return Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'country` ADD `id_currency` INT NOT NULL DEFAULT \'0\' AFTER `id_zone`');
	return true;
}
