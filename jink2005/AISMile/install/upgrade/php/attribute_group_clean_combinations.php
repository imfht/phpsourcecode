<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function attribute_group_clean_combinations()
{
	$attributeCombinations = Db::getInstance()->executeS('SELECT
		pac.`id_attribute`, pa.`id_product_attribute` 
		FROM `'._DB_PREFIX_.'product_attribute` pa 
		LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac 
			ON (pa.`id_product_attribute` = pac.`id_product_attribute`)');
	$toRemove = array();
	foreach ($attributeCombinations AS $attributeCombination)
		if ((int)($attributeCombination['id_attribute']) == 0)
			$toRemove[] = (int)($attributeCombination['id_product_attribute']);

	if (!empty($toRemove))
	{
		$res = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_attribute`
			WHERE `id_product_attribute` IN ('.implode(', ', $toRemove).')');
		return $res;
	}
	return true;
}
