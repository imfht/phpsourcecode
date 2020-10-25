<?php
namespace Ysf;
/**
 * config class
 * @update 20160107171940
 */
class Config
{
	public static $config;
	/**
	 * init config
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public static function init()
	{
		if (empty(self::$config)) {
			self::load_config();
		}
		return true;	
	}

	public static function load_config($refresh=false){
		# runtime processing, dev config not write into cache 
		if (APP_MODE=='DEV' || $refresh) {
			
		}else{
			self::$config = cache('__common_config__');
			if (self::$config!=false) {
				return false;	
			}
		}
		# loading ysf config
		self::$config = include YSF_PATH.'/Conf/config.php';
		# loading app config
		$dir = APP_PATH.'/config';
		$handle = opendir($dir);
		if ($handle) {
			while ($file=readdir($handle)) {
				if ($file == ".." && $file == "."){
					continue;
				}
				if (is_dir($dir . "/" . $file)){
					continue;
				}
				$file_info = explode('.', $file);
				if (end($file_info)!='php') {
					continue;
				}
				# include APP_MODE config
				if (strtolower($file_info[count($file_info)-2])!=strtolower(APP_MODE)) {
					continue;
				}
				$_config = include $dir.'/'.$file;
				if (!is_array($_config)) {
					continue;
				}
				self::$config = array_merge(self::$config,$_config);
				unset($_config,$file_info);
			}
			closedir($handle);
		}
		unset($handle);
		cache('__common_config__', self::$config);
	}
	
	/**
	 * set config
	 * @return  bool 
	 */
	public static function set(string $key,$value)
	{
		$key = explode('/', $key);
		$p = &self::$config;
		foreach ($key as $k) {
			if(!isset($p[$k]) || !is_array($p[$k])) {
				$p[$k] = [];
			}
			$p = &$p[$k];
		}
		$p = $value;
		unset($p);
		return true;	
	}

	/**
	 * get config
	 * @param string $key
	 */
	public static function get($key=null)
	{
		if ($key===null) {
			return self::$config;
		}else{
			$key = explode('/', $key);
			$v = &self::$config;
			foreach ($key as $k) {
				if (!isset($v[$k])) {
					return null;
				}
				$v = &$v[$k];
			}
			return $v;
		}
	}

}