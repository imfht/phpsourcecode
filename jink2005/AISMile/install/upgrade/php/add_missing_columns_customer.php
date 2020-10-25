<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_missing_columns_customer()
{
	$db = Db::getInstance();
	$res = true;
	$current_fields = $db->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'customer`');
	foreach ($current_fields as $k => $field)
		$current_fields[$k] = $field['Field'];

	$missing_fields = array(
		'id_risk' => 'ALTER TABLE `'._DB_PREFIX_.'customer` 
			ADD `id_risk` int(10) unsigned NOT NULL DEFAULT "1"',
		'company' => 'ALTER TABLE `'._DB_PREFIX_.'customer` ADD `company` varchar(64)',
		'siret' => 'ALTER TABLE `'._DB_PREFIX_.'customer` ADD `siret` varchar(14)',
		'ape' => 'ALTER TABLE `'._DB_PREFIX_.'customer` ADD `ape` varchar(5)',
		'website' => 'ALTER TABLE `'._DB_PREFIX_.'customer` ADD `website` varchar(128)',
		'outstanding_allow_amount' => 'ALTER TABLE `'._DB_PREFIX_.'customer`
			ADD `outstanding_allow_amount` DECIMAL( 10,6 ) NOT NULL default "0.00"',
		'show_public_prices' => 'ALTER TABLE `'._DB_PREFIX_.'customer`
			ADD `show_public_prices` tinyint(1) unsigned NOT NULL default "0"',
		'max_payment_days' => 'ALTER TABLE `'._DB_PREFIX_.'customer`
			ADD `max_payment_days` int(10) unsigned NOT NULL default "60"'
	);
	
	foreach ($missing_fields as $field => $query)
		if (!in_array($field, $current_fields))
			$res &= $db->execute($query);

	return $res;
}
