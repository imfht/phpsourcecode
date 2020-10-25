<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function updateproductcomments()
{
	if (Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'product_comment') !== false)
	{
		Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'product_comment_criterion_lang (
											`id_product_comment_criterion` INT( 11 ) UNSIGNED NOT NULL ,
											`id_lang` INT(11) UNSIGNED NOT NULL ,
											`name` VARCHAR(64) NOT NULL ,
											PRIMARY KEY ( `id_product_comment_criterion` , `id_lang` )
											) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');
		Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'product_comment_criterion_category (
											  `id_product_comment_criterion` int(10) unsigned NOT NULL,
											  `id_category` int(10) unsigned NOT NULL,
											  PRIMARY KEY(`id_product_comment_criterion`, `id_category`),
											  KEY `id_category` (`id_category`)
											) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');
		Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'product_comment ADD `id_guest` INT(11) NULL AFTER `id_customer`');
		Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'product_comment ADD `customer_name` varchar(64) NULL AFTER `content`');
		Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'product_comment ADD `deleted` tinyint(1) NOT NULL AFTER `validate`');
		Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'product_comment ADD INDEX (id_customer)');
		Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'product_comment ADD INDEX (id_guest)');
		Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'product_comment ADD INDEX (id_product)');
		Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'product_comment_criterion DROP `id_lang`');
		Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'product_comment_criterion DROP `name`');
		Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'product_comment_criterion ADD `id_product_comment_criterion_type` tinyint(1) NOT NULL AFTER `id_product_comment_criterion`');
		Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'product_comment_criterion ADD `active` tinyint(1) NOT NULL AFTER `id_product_comment_criterion_type`');
		Db::getInstance()->execute('ALTER IGNORE TABLE `'._DB_PREFIX_.'product_comment` ADD `title` VARCHAR(64) NULL AFTER `id_guest`;');
	}
}


