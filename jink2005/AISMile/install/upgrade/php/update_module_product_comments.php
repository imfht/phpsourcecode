<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function update_module_product_comments()
{
	if (Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name`="productcomments"'))
	{
		Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'product_comment_usefulness` (
			  `id_product_comment` int(10) unsigned NOT NULL,
			  `id_customer` int(10) unsigned NOT NULL,
			  `usefulness` tinyint(1) unsigned NOT NULL,
			  PRIMARY KEY (`id_product_comment`, `id_customer`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');

		Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'product_comment_report` (
			  `id_product_comment` int(10) unsigned NOT NULL,
			  `id_customer` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`id_product_comment`, `id_customer`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');
	}
}

