<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

// backward compatibility vouchers should be available in all categories
function update_module_loyalty()
{
	$ps_loyalty_point_value = Db::getInstance()->getValue('SELECT value 
		FROM `'._DB_PREFIX_.'configuration`
		WHERE name="PS_LOYALTY_POINT_VALUE"');
	if ($ps_loyalty_point_value !== false)
	{
		$category_list = '';
		$categories = Db::getInstance('SELECT id_category FROM `'._DB_PREFIX_.'category`');
		foreach($categories as $category)
			$category_list .= $category['id_category'].',';

		if (!empty($category_list))
		{
			$category_list = rtrim($category_list, ',');
			$res &= Db::getInstance()->execute('REPLACE INTO `'._DB_PREFIX_.'configuration`
			(name, value) VALUES ("PS_LOYALTY_VOUCHER_CATEGORY", "'.$category_list.'"');
		}
	}
}

