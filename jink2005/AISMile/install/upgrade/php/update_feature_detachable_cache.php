<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function update_feature_detachable_cache()
{
	$array_features = array(
		'PS_SPECIFIC_PRICE_FEATURE_ACTIVE' => 'specific_price',
		'PS_SCENE_FEATURE_ACTIVE' => 'scene',
		'PS_PRODUCT_DOWNLOAD_FEATURE_ACTIVE' => 'product_download',
		'PS_CUSTOMIZATION_FEATURE_ACTIVE' => 'customization_field',
		'PS_CART_RULE_FEATURE_ACTIVE' => 'discount',
		'PS_GROUP_FEATURE_ACTIVE' => 'group',
		'PS_PACK_FEATURE_ACTIVE' => 'pack',
		'PS_ALIAS_FEATURE_ACTIVE' => 'alias',
	);
	$res = true;
	foreach ($array_features as $config_key => $feature)
	{
		// array_features is an array defined above, so please don't add bqSql !
		$count = (int)Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.$feature.'`');
		$res &= Db::getInstance()->execute('REPLACE INTO `'._DB_PREFIX_.'configuration` (name, value) values ("'.$config_key.'", "'.$count.'")');

	}
	return $res;
}

