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
 * Check if all needed columns in webservice_account table exists.
 * These columns are used for the WebserviceRequest overriding.
 * 
 * @return void
 */
function check_webservice_account_table()
{
	$sql = 'SHOW COLUMNS FROM '._DB_PREFIX_.'webservice_account';
	$return = DB::getInstance()->executeS($sql);
	if (count($return) < 7)
	{
		$sql = 'ALTER TABLE `'._DB_PREFIX_.'webservice_account` ADD `is_module` TINYINT( 2 ) NOT NULL DEFAULT \'0\' AFTER `class_name` ,
		ADD `module_name` VARCHAR( 50 ) NULL DEFAULT NULL AFTER `is_module`';
		DB::getInstance()->executeS($sql);
	}
}