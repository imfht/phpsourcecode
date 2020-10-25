<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function alter_productcomments_guest_index()
{
	$id_productcomments = Db::getInstance()->getValue('SELECT id_module 
		FROM  `'._DB_PREFIX_.'module` WHERE name = "productcomments"');

	if (!$id_productcomments)
		return;
	
	DB::getInstance()->execute('
	ALTER TABLE `'._DB_PREFIX_.'product_comment`
	DROP INDEX `id_guest`, ADD INDEX `id_guest` (`id_guest`);');
}

