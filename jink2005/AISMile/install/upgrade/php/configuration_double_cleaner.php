<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function configuration_double_cleaner()
{
	$result = Db::getInstance()->executeS('
	SELECT name, MIN(id_configuration) AS minid
	FROM '._DB_PREFIX_.'configuration
	GROUP BY name
	HAVING count(name) > 1');
	foreach ($result as $row)
	{
		DB::getInstance()->execute('
		DELETE FROM '._DB_PREFIX_.'configuration
		WHERE name = \''.addslashes($row['name']).'\'
		AND id_configuration != '.(int)($row['minid']));
	}
	DB::getInstance()->execute('
	DELETE FROM '._DB_PREFIX_.'configuration_lang
	WHERE id_configuration NOT IN (
		SELECT id_configuration
		FROM '._DB_PREFIX_.'configuration)');
}

