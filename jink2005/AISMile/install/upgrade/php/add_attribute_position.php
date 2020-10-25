<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_attribute_position()
{
	$groups = Db::getInstance()->executeS('
	SELECT DISTINCT `id_attribute_group`
	FROM `'._DB_PREFIX_.'attribute`');
	if (count($groups) && is_array($groups))
		foreach ($groups as $group)
		{
			$attributes = Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'attribute`
			WHERE `id_attribute_group` = '. (int)($group['id_attribute_group']));
			$i = 0;
			if (count($attributes) && is_array($attributes))
				foreach ($attributes as $attribute)
				{
					Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'attribute` 
					SET `position` = '.$i++.'
					WHERE `id_attribute` = '.(int)$attribute['id_attribute'].'
					AND `id_attribute_group` = '.(int)$attribute['id_attribute_group']);
				}
		}
}