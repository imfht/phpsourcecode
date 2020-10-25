<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */
function cms_block()
{
	if (!Db::getInstance()->execute('SELECT `display_store` FROM `'._DB_PREFIX_.'cms_block` LIMIT 1'))
		return Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'cms_block` ADD `display_store` TINYINT NOT NULL DEFAULT \'1\'');
	return true;
}

