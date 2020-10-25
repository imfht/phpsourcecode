<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_missing_rewrite_value()
{
	$pages = Db::getInstance()->executeS('
	SELECT * 
	FROM `'._DB_PREFIX_.'meta` m
	LEFT JOIN `'._DB_PREFIX_.'meta_lang` ml ON (m.`id_meta` = ml.`id_meta`)
	WHERE ml.`url_rewrite` = \'\'
	AND m.`page` != "index"
	');
	if (sizeof($pages) && is_array($pages))
		foreach ($pages as $page)
		{
			Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'meta_lang`
			SET `url_rewrite` = "'.pSQL(Tools::str2url($page['title'])).'"
			WHERE `id_meta` = '.(int)$page['id_meta'].'
			AND `id_lang` = '.(int)$page['id_lang']);
		}
}