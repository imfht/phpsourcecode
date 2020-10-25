<?php
namespace Ysf;
/**
 * Cache class
 */
class Cache
{
	private static $cache_mode='file';

	public static function init(){

	}
	/**
	 * get cache
	 * @param  string $key 
	 * @return string
	 */
	public static function get(string $key)
	{
		$cache_string = self::read($key);
		if ($cache_string===false) {
			return false;
		}
		$dejson = json_decode($cache_string,true);
		if ($dejson) {
			return $dejson;
		}else{
			return $cache_string;
		}
	}

	/**
	 * set cache
	 * @param [type] $key   [description]
	 * @param [type] $value [description]
	 */
	public static function set($key,$value)
	{
		if (!is_string($key)) {
			throw new Exception("cache key must be string", 1);
		}
		if (is_array($value)) {
			$value = json_encode($value);
		}
		return self::write($key,$value);
	}

	private static function write($key,$value){
		switch (self::$cache_mode) {
			case 'file':
			default:
				return file_put_contents(RUNTIME_PATH.'/cache/'.$key, $value);
				break;
		}
	}

	private static function read($key){
		switch (self::$cache_mode) {
			case 'file':
			default:
				$path = RUNTIME_PATH.'/cache/'.$key;
				if (!file_exists($path)) {
					return false;
				}
				return file_get_contents($path);
				break;
		}
	}
}