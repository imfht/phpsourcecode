<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function generate_ntree()
{
	$categories = Db::getInstance()->executeS('SELECT id_category, id_parent FROM '._DB_PREFIX_.'category ORDER BY id_parent ASC, position ASC');
	$categoriesArray = array();
	foreach ($categories AS $category)
		$categoriesArray[(int)$category['id_parent']]['subcategories'][(int)$category['id_category']] = 1;
	$n = 1;
	generate_ntree_subTree($categoriesArray, 1, $n);
}

function generate_ntree_subTree(&$categories, $id_category, &$n)
{
	$left = (int)$n++;
	if (isset($categories[(int)$id_category]['subcategories']))
		foreach (array_keys($categories[(int)$id_category]['subcategories']) AS $id_subcategory)
			generate_ntree_subTree($categories, (int)$id_subcategory, $n);
	$right = (int)$n++;

	Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'category
		SET nleft = '.(int)$left.', nright = '.(int)$right.' 
		WHERE id_category = '.(int)$id_category.' LIMIT 1');
}
