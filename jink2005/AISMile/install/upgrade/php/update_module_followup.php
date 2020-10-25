<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function update_module_followup()
{
	$id_followup = Db::getInstance()->getValue('SELECT id_module FROM  `'._DB_PREFIX_.'module` WHERE name = "followup"');
	if (!$id_followup)
		return;

	Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'log_email` ADD INDEX `date_add`(`date_add`), ADD INDEX `id_cart`(`id_cart`);');
}

