<?php
class AutoLoad {
	public static function load($className) {
		$className = str_replace('\\', '/', substr($className, 3));
		
		$filename = __DIR__.'/'.$className.'.php';
		
		if(is_file($filename)) {
			include($filename);
			return true;
		}
		
		return false;
	}
}

spl_autoload_register(array('\AutoLoad','load'));