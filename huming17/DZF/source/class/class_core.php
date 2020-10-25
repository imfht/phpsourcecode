<?php

error_reporting(E_ALL);

define('IN_DZF', true);
define('DZF_CORE_DEBUG', false);

set_exception_handler(array('core', 'handleException'));

if(DZF_CORE_DEBUG) {
	set_error_handler(array('core', 'handleError'));
	register_shutdown_function(array('core', 'handleShutdown'));
}

if(function_exists('spl_autoload_register')) {
	spl_autoload_register(array('core', 'autoload'));
} else {
	function __autoload($class) {
		return core::autoload($class);
	}
}

//C::creatapp();

class core
{
	private static $_tables;
	private static $_imports;
	private static $_app;
	private static $_memory;

	public static function app() {
		return self::$_app;
	}

	public static function creatapp() {
		if(!is_object(self::$_app)) {
			self::$_app = core_application::instance();
		}
		return self::$_app;
	}

	public static function t($name) {
		$pluginid = null;
		if($name[0] === '#') {
			list(, $pluginid, $name) = explode('#', $name);
		}
		$classname = 'table_'.$name;
		if(!isset(self::$_tables[$classname])) {
			if(!class_exists($classname, false)) {
				self::import(($pluginid ? 'plugin/'.$pluginid : 'class').'/table/'.$name);
			}
			self::$_tables[$classname] = new $classname;
		}
		return self::$_tables[$classname];
	}

	public static function memory() {
		if(!self::$_memory) {
			self::$_memory = new core_memory();
			self::$_memory->init(self::app()->config['memory']);
		}
		return self::$_memory;
	}

	public static function import($name, $folder = '', $force = true) {
		$key = $folder.$name;
		if(!isset(self::$_imports[$key])) {
			$path = DZF_ROOT.'/source/'.$folder;
			if(strpos($name, '/') !== false) {
				$pre = basename(dirname($name));
				$filename = dirname($name).'/'.$pre.'_'.basename($name).'.php';
			} else {
				$filename = $name.'.php';
			}

			if(is_file($path.'/'.$filename)) {
				self::$_imports[$key] = true;
				return include $path.'/'.$filename;
			} elseif(!$force) {
				return false;
			} else {
				//TODO DEBUG 此处为何和PHPEXCEL冲突 有待研究
				//throw new Exception('Oops! System file lost: '.$filename);
			}
		}
		return true;
	}

	public static function handleException($exception) {
		core_error::exception_error($exception);
	}


	public static function handleError($errno, $errstr, $errfile, $errline) {
		if($errno & DZF_CORE_DEBUG) {
			core_error::system_error($errstr, false, true, false);
		}
	}

	public static function handleShutdown() {
		if(($error = error_get_last()) && $error['type'] & DZF_CORE_DEBUG) {
			core_error::system_error($error['message'], false, true, false);
		}
	}

	public static function autoload($class) {
		$class = strtolower($class);
		if(strpos($class, '_') !== false) {
			list($folder) = explode('_', $class);
			$file = 'class/'.$folder.'/'.substr($class, strlen($folder) + 1);
		} else {
			$file = 'class/'.$class;
		}

		try {

			self::import($file);
			return true;

		} catch (Exception $exc) {

			$trace = $exc->getTrace();
			foreach ($trace as $log) {
				if(empty($log['class']) && $log['function'] == 'class_exists') {
					return false;
				}
			}
			core_error::exception_error($exc);
		}
	}
}

class C extends core {}
class DB extends core_database {}

?>