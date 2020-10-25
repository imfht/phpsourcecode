<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function p15014_upgrade_sekeywords()
{
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'sekeyword` ADD id_shop INTEGER UNSIGNED NOT NULL DEFAULT 1 AFTER id_sekeyword');
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'sekeyword` ADD id_shop_group INTEGER UNSIGNED NOT NULL DEFAULT 1 AFTER id_shop');
	return true;
}
