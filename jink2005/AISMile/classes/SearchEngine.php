<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class SearchEngineCore extends ObjectModel
{
	public $server;
	public $getvar;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'search_engine',
		'primary' => 'id_search_engine',
		'fields' => array(
			'server' => array('type' => self::TYPE_STRING, 'validate' => 'isUrl', 'required' => true),
			'getvar' => array('type' => self::TYPE_STRING, 'validate' => 'isModuleName', 'required' => true),
		),
	);

	public static function getKeywords($url)
	{
		$parsed_url = @parse_url($url);
		if (!isset($parsed_url['host']) || !isset($parsed_url['query']))
			return false;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `server`, `getvar` FROM `'._DB_PREFIX_.'search_engine`');
		foreach ($result as $row)
		{
			$host =& $row['server'];
			$varname =& $row['getvar'];
			if (strstr($parsed_url['host'], $host))
			{
				$array = array();
				preg_match('/[^a-z]'.$varname.'=.+\&/U', $parsed_url['query'], $array);
				if (empty($array[0]))
					preg_match('/[^a-z]'.$varname.'=.+$/', $parsed_url['query'], $array);
				if (empty($array[0]))
					return false;
				$str = urldecode(str_replace('+', ' ', ltrim(substr(rtrim($array[0], '&'), strlen($varname) + 1), '=')));
				return $str;
			}
		}
	}
}


