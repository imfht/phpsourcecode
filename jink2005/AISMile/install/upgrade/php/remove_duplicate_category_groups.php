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
 * Removes duplicates from table category_group caused by a bug in category importing in PS < 1.4.2
 */
function remove_duplicate_category_groups()
{
	$result = Db::getInstance()->executeS('
		SELECT `id_category`, `id_group`, COUNT(*) as `count`
		FROM `'._DB_PREFIX_.'category_group`
		GROUP BY `id_category`, `id_group`
		ORDER BY `count` DESC');
	
	foreach($result as $row)
	{
		if ((int)$row['count'] > 1)
		{
			$limit = (int)$row['count'] - 1; 
			$result = Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'category_group`
				WHERE `id_category` = '.$row['id_category'].' AND `id_group` = '.$row['id_group'].'
				LIMIT '.$limit);			
		}
		else
			return;
	}
}