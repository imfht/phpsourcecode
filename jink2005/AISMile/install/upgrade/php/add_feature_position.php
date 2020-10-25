<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_feature_position()
{
	$features = Db::getInstance()->executeS('
	SELECT `id_feature`
	FROM `'._DB_PREFIX_.'feature`');
	$i = 0;
	if (sizeof($features) && is_array($features))
		foreach ($features as $feature)
		{
			Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'feature` 
			SET `position` = '.$i++.'
			WHERE `id_feature` = '.(int)$feature['id_feature']);
		}
}