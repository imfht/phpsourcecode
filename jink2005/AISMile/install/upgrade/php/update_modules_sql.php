<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function update_modules_sql()
{
	$id_blocklink = Db::getInstance()->getValue('SELECT id_module FROM  `'._DB_PREFIX_.'module` WHERE name = "blocklink"');
	if ($id_blocklink)
		Db::getInstance()->execute('ALTER IGNORE TABLE `'._DB_PREFIX_.'blocklink_lang` ADD PRIMARY KEY (`id_link`, `id_lang`);');
	$id_productcomments = Db::getInstance()->getValue('SELECT id_module FROM  `'._DB_PREFIX_.'module` WHERE name = "productcomments"');
	if ($id_productcomments)
	{
		Db::getInstance()->execute('ALTER IGNORE TABLE `'._DB_PREFIX_.'product_comment_grade` ADD PRIMARY KEY (`id_product_comment`, `id_product_comment_criterion`);');
		Db::getInstance()->execute('ALTER IGNORE TABLE `'._DB_PREFIX_.'product_comment_criterion` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_product_comment_criterion`, `id_lang`);');
		Db::getInstance()->execute('ALTER IGNORE TABLE `'._DB_PREFIX_.'product_comment_criterion_product` ADD PRIMARY KEY(`id_product`, `id_product_comment_criterion`);');
	}
}
