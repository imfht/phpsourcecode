<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function alter_cms_block()
{
	// No one will know if the table does not exist :] Thanks Damien for your solution ;)
	DB::getInstance()->execute('ALTER TABLE  `'._DB_PREFIX_.'cms_block_lang` CHANGE  `id_block_cms`  `id_cms_block` INT( 10 ) UNSIGNED NOT NULL');
	
	DB::getInstance()->execute('ALTER TABLE  `'._DB_PREFIX_.'cms_block` CHANGE  `id_block_cms`  `id_cms_block` INT( 10 ) UNSIGNED NOT NULL');
	
	DB::getInstance()->execute('ALTER TABLE  `'._DB_PREFIX_.'cms_block_page` CHANGE  `id_block_cms`  `id_cms_block` INT( 10 ) UNSIGNED NOT NULL');
	
	DB::getInstance()->execute('ALTER TABLE  `'._DB_PREFIX_.'cms_block_page` CHANGE  `id_block_cms_page`  `id_cms_block_page` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT');
		
}

