<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/**
 * Regenerate the entire category tree level_depth
 */
function regenerate_level_depth()
{
	Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'category` SET `level_depth` = 0 WHERE `id_category` = 1');
	regenerate_children_categories(1, 0);
}

/**
 * Recursively regenerate the level_depth of this category's children
 *
 * @param int $id_category
 * @param int $level_depth
 */
function regenerate_children_categories($id_category, $level_depth)
{
	$categories = Db::getInstance()->executeS('SELECT `id_category` FROM `'._DB_PREFIX_.'category` WHERE `id_parent` = '.(int)$id_category);
	if (!$categories)
		return;
	$new_depth = (int)$level_depth + 1;
	$cat_ids = "";
	foreach($categories as $category)
	{
		$cat_ids .= (string)$category['id_category'].',';
		regenerate_children_categories($category['id_category'], $new_depth);
	}
	$cat_ids = substr($cat_ids, 0, -1);

	Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'category` SET `level_depth` = '.(int)$new_depth.' WHERE `id_category` IN ('.$cat_ids.')');
}
