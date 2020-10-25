<?php
namespace Ysf;
/**
 * Core class 
 */
class Ysf
{
	private static $_map = [];
	
	public static function init()
	{
		self::init_define();
		self::init_first_run();
		self::init_include();
		self::init_setting();
		self::init_route();
		self::run();
	}


	public static function init_define()
	{
		// init app mode ('DEV','TEST','RPOD')
		defined('APP_MODE')===false && define('APP_MODE', 'DEV');
		// init app path
		defined('APP_PATH')===false && define('APP_PATH', TOP_PATH.'/app');
		// init runtime path
		defined('RUNTIME_PATH')===false && define('RUNTIME_PATH', TOP_PATH.'/runtime');
	}
	
	/**
	 * call app
	 * @return [type] [description]
	 */
	public static function run()
	{
		$controller_full_name = 'App\\Controller\\controller_'.CONTROLLER_NAME;
		call_user_func(array(new $controller_full_name,ACTION_NAME));
	}

	/**
	 * including class
	 * @return [type] [description]
	 */
	public static function init_include()
	{	
		$core_classes = array(
			'View'		=> YSF_PATH.'/Library/View.class.php',
			'Route'		=> YSF_PATH.'/Library/Route.class.php',
			'Model'		=> YSF_PATH.'/Library/Model.class.php',
			'Controller'=> YSF_PATH.'/Library/Controller.class.php',
			'Cache'		=> YSF_PATH.'/Library/Cache.class.php',
			'Error'		=> YSF_PATH.'/Library/Error.class.php',
			'Config'	=> YSF_PATH.'/Library/Config.class.php',
			'Uploader'	=> YSF_PATH.'/Library/Uploader.class.php',
			'Smarty'	=> YSF_PATH.'/Vendor/Smarty/Smarty.class.php',
			'Mysql'		=> YSF_PATH.'/Library/DB/Mysql.class.php',
		);
		foreach ($core_classes as $k => $v) {
			include_once $v;
		}
		unset($core_classes);
	}

	/**
	 * init setting
	 * @return [type] [description]
	 */
	public static function init_setting()
	{	
		// set powered
		header("Powered:Ysf");
		// set error handle
		set_error_handler('\Ysf\Error::app_error');
		set_exception_handler('\Ysf\Error::app_exception');	
		register_shutdown_function('\Ysf\Ysf::app_shutdown');

		// init config
		\Ysf\Config::init();

		// set timezone
		date_default_timezone_set(config('timezone'));
		
	}

	/**
	 * first run
	 * @return [type] [description]
	 */
	public static function init_first_run()
	{
		#TODO debug?
		if (file_exists(APP_PATH)===false) {
			mkdir(APP_PATH);
			mkdir(APP_PATH.'/controller');
			mkdir(APP_PATH.'/model');
			mkdir(APP_PATH.'/view');
			mkdir(APP_PATH.'/config');
			file_put_contents(APP_PATH.'/controller/controller_index.php', file_get_contents(YSF_PATH.'/Conf/controller_index.php.demo'));
		}		
		if (file_exists(RUNTIME_PATH)===false) {
			mkdir(RUNTIME_PATH);
			mkdir(RUNTIME_PATH.'/template');
			mkdir(RUNTIME_PATH.'/log');
			mkdir(RUNTIME_PATH.'/cache');
		}
	}

	/**
	 * init route
	 * @return [type] [description]
	 */
	public static function init_route()
	{
		\Ysf\Route::init();
		#TODO perfect route
		define('CONTROLLER_NAME',\Ysf\Route::get_controller());
		define('ACTION_NAME',\Ysf\Route::get_action());
	}

	/**
	 * shutdown handle
	 * @return [type] [description]
	 */
	public static function app_shutdown(){
	}
}

