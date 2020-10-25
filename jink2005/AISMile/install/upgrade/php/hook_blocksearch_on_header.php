<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function hook_blocksearch_on_header()
{
	if ($id_module = Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name` = \'blocksearch\''))
	{
		$id_hook = Db::getInstance()->getValue('
			SELECT `id_hook`
			FROM `'._DB_PREFIX_.'hook`
			WHERE `name` = \'header\'
		');
		
		$position = Db::getInstance()->getValue('
			SELECT MAX(`position`)
			FROM `'._DB_PREFIX_.'hook_module`
			WHERE `id_hook` = '.(int)$id_hook.'
		');
		
		Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'hook_module` (`id_module`, `id_hook`, `position`) 
			VALUES ('.(int)$id_module.', '.(int)$id_hook.', '.($position+1).')
		');
	}
}