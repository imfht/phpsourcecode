<?php
defined('BASE_PATH') or define('BASE_PATH', dirname(__FILE__) . '/');
defined('CP_PATH') or define('CP_PATH', dirname(__FILE__) . '/include/');
defined('ROOT_PATH') or define('ROOT_PATH', realpath(dirname(__FILE__) . '/../') . '/');
defined('DEFAULT_APP') or define('DEFAULT_APP', 'default');
defined('DEFAULT_CONTROLLER') or define('DEFAULT_CONTROLLER', 'index');
defined('DEFAULT_ACTION') or define('DEFAULT_ACTION', 'index');
	
function urlRoute(){
	$rewrite = config('REWRITE');
	if( !empty($rewrite) ) {
		if( ($pos = strpos( $_SERVER['REQUEST_URI'], '?' )) !== false ){
			parse_str( substr( $_SERVER['REQUEST_URI'], $pos + 1 ), $_GET );
		}
		foreach($rewrite as $rule => $mapper){
			$rule = ltrim($rule, "./\\");
			if( false === stripos($rule, 'http://')){
				$rule = $_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/\\') . '/' . $rule;
			}
			$rule = '/'.str_ireplace(array('\\\\', 'http://', '-', '/', '<', '>',  '.'), array('', '', '\-', '\/', '(?<', ">[a-z0-9_%]+)", '\.'), $rule).'/i';
			if( preg_match($rule, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $matches) ){
				foreach($matches as $matchkey => $matchval){
					if(('app' === $matchkey)){
						$mapper = str_ireplace('<app>', $matchval, $mapper);
					}else if('c' === $matchkey){
						$mapper = str_ireplace('<c>', $matchval, $mapper);
					}else if('a' === $matchkey){
						$mapper = str_ireplace('<a>', $matchval, $mapper);
					} else {
						if( !is_int($matchkey) ) $_GET[$matchkey] = $matchval;
					}
				}
				$_REQUEST['r'] = $mapper;
				break;
			}
		}
	}
	
	$route_arr = isset($_REQUEST['r']) ? explode("/", $_REQUEST['r']) : array();
	$app_name = empty($route_arr[0]) ? DEFAULT_APP : strtolower($route_arr[0]);
	$controller_name = empty($route_arr[1]) ? DEFAULT_CONTROLLER : strtolower($route_arr[1]);
	$action_name = empty($route_arr[2]) ? DEFAULT_ACTION : $route_arr[2];
	$_REQUEST['r'] = $app_name .'/'. $controller_name .'/'. strtolower($action_name);
	
	define('APP_NAME', $app_name);
	define('CONTROLLER_NAME', $controller_name);
	define('ACTION_NAME', $action_name);
}

function url($route='index/index', $params=array()){
	if( count( explode('/', $route) ) < 3 )  $route = config('_APP_NAME') . '/' . $route;
	$param_str = empty($params) ? '' : '&' . http_build_query($params);
	$url = $_SERVER["SCRIPT_NAME"] . '?r=' . $route . $param_str;
	
	static $rewrite = array();
	if( empty($rewrite) ) $rewrite = config('REWRITE');
	
	if( !empty($rewrite) ){
		static $urlArray = array();
		if( !isset($urlArray[$url]) ){
			foreach($rewrite as $rule => $mapper){
				$mapper = '/'.str_ireplace(array('/', '<app>', '<c>', '<a>'), array('\/', '(?<app>\w+)', '(?<c>\w+)', '(?<a>\w+)'), $mapper).'/i';
				if( preg_match($mapper, $route, $matches) ){
					list($app, $controller, $action) = explode('/', $route);
					$urlArray[$url] = str_ireplace(array('<app>', '<c>', '<a>'), array($app, $controller, $action), $rule);
					if( !empty($params) ){
						$_args = array();
						foreach($params as $argkey => $arg){
							$count = 0;
							$urlArray[$url] = str_ireplace('<'.$argkey.'>', $arg, $urlArray[$url], $count);
							if(!$count) $_args[$argkey] = $arg;
						}
						//处理多出来的参数
						if( !empty($_args) ){
							$urlArray[$url] = preg_replace('/<\w+>/', '', $urlArray[$url]). '?' . http_build_query($_args);
						}	
					}
					//自动加上域名
					if(false === stripos($urlArray[$url], 'http://')){
						$urlArray[$url] = 'http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER["SCRIPT_NAME"]), "./\\") .'/'.ltrim($urlArray[$url], "./\\");
					}
					
					//参数个数匹配则返回
					$rule = str_ireplace(array('<app>', '<c>', '<a>'), '', $rule);
					if( count($params) == preg_match_all('/<\w+>/is', $rule, $_match)){
						return $urlArray[$url];
					}	
				}
			}
			return isset($urlArray[$url]) ? $urlArray[$url] : $url;
		}
		return $urlArray[$url];
	}
	return $url;
}

function autoload($className){
	$array = array(
					BASE_PATH . 'apps/' . config('_APP_NAME') . '/model/' . $className . '.php',
					BASE_PATH . 'apps/' . config('_APP_NAME') . '/controller/' . $className . '.php',				
					BASE_PATH . 'base/model/' . $className . '.php',
					BASE_PATH . 'base/controller/' . $className . '.php',
					BASE_PATH . 'base/api/' . $className . '.php',
					BASE_PATH . 'base/extend/' . $className . '.php',
					CP_PATH . 'core/' . $className . '.class.php',
					CP_PATH . 'lib/' . $className . '.class.php',
					CP_PATH . 'ext/' . $className . '.class.php',
					CP_PATH . 'ext/alichat/' . $className . '.class.php',//T-Team添加
					//CP_PATH . 'ext/uc_client/client.php',//T-Team添加ucenter接口			
	);
	foreach($array as $file){
		if( is_file($file)){
			require_once($file);
			return true;
		}
	}
	return false;
}

function getApps($noapp = array()){
	static $appsArray = array();
	if( empty($apisArray) ){
		foreach(glob(BASE_PATH . 'apps/*/config.php') as $file){
			if( preg_match('#apps/(.*?)/config.php#', $file, $matches)){
				$appsArray[] = $matches[1];
			}
		}
	}
	return array_diff($appsArray,$noapp);
}

function appConfig($app){
	static $appConfig = array();
	if( !isset( $appConfig[$app]) ){
		if( is_file(BASE_PATH . 'apps/' . $app . '/config.php') ){
			$appConfig[$app] = require(BASE_PATH . 'apps/' . $app . '/config.php');
		}else{
			$appConfig[$app] = array();
		}
	}
	return $appConfig[$app];
}

function config($name=NULL, $value=NULL){
	static $config = array();
	$argsNum = func_num_args();
	if( 0 == $argsNum ){
		return $config;
	}else if( 1 == $argsNum ){
		if( is_array($name)){
			foreach($name as $k => $v){
				if( is_array($v) ){
					isset($config[$k]) or $config[$k] = array();
					$config[$k] = array_merge($config[$k], $v);
				} else {
					$config[$k] = $v;
				}
			}
			return $config;
		}else if(isset($config[$name])) {
			return $config[$name];
		} else if(isset($config['APP'][$name])) {
			return $config['APP'][$name];
		} else if(isset($config['DB'][$name])) {
			return $config['DB'][$name];			
		} else if(isset($config['TPL'][$name])) {
			return $config['TPL'][$name];
		} else {
			return NULL;
		}
	} else {
		return $config[$name] = is_array($value) ? array_merge($config[$name], $value) : $value;
	}
}

function model($model){
	static $objArray = array();
	$className = $model . 'Model';
	if( !is_object($objArray[$className]) ){
		if( !class_exists($className) ) {
			throw new Exception(config('_APP_NAME'). '/' . $className . '.php 模型类不存在');
		}
		$objArray[$className] = new $className();
	}
	return $objArray[$className];
}

function api($app, $method = '', $params = array()){	
	if( empty($app) || empty($method) ) return false;
	
	$apis =array(); 
	if( is_array($app) ){
		$isArray = true;
		$apis = $app;
	}else{
		$isArray = false;
		$apis[] = $app;
	}
	
	static $objArray = array();
	$returnData = array();
	$currentConfig = config();
	foreach($apis as $app){
		config('_APP_NAME', $app);
		$config = appConfig($app);
		$className = $app . 'Api';
		if( !is_object($objArray[$className]) ){
			
			if( empty( $config['APP_STATE'] ) || (1 != $config['APP_STATE'] )) continue;
			$file = BASE_PATH . 'apps/' . $app . '/'. $className.'.php'; 
			if( !is_file($file) ) continue;
			require_once($file);
			if( !class_exists($className) ) continue;
			config($config);			
			$objArray[$className] = new $className();
		}
		if( !method_exists($objArray[$className], $method) ) continue;
		config($config);
		$returnData[$app] = call_user_func_array( array($objArray[$className], $method), $params);
		config('_APP_NAME', APP_NAME);
		config($currentConfig);
	}
	config('_APP_NAME', APP_NAME);
	config($currentConfig);
	return $isArray ? $returnData : ( isset($returnData[$app]) ? $returnData[$app] : NULL );
}

function run(){
	require(CP_PATH . 'core/cpConfig.class.php');//加载默认配置
	config( require(BASE_PATH . 'conf/config.php') );//加载全局配置
	cpConfig::set('APP', array_merge(cpConfig::get('APP'), config('APP')));	
	defined('DEBUG') or define('DEBUG', cpConfig::get('DEBUG'));
	date_default_timezone_set( cpConfig::get('TIMEZONE') );
	
	if ( DEBUG ) {
		ini_set("display_errors", 1);
		error_reporting( E_ALL ^ E_NOTICE );//除了notice提示，其他类型的错误都报告
	} else {
		ini_set("display_errors", 0);
		error_reporting(0);//把错误报告，全部屏蔽
	}
	
	urlRoute();//网址路由解析
	config('_APP_NAME', APP_NAME);
	config( appConfig(APP_NAME) ); //加载app配置
		
	try{
		defined('__ROOT__') or define('__ROOT__', config('URL_HTTP_HOST') . rtrim(dirname($_SERVER["SCRIPT_NAME"]), '\\/'));
		defined('__PUBLIC__') or define('__PUBLIC__', __ROOT__ . '/' . 'public');
		defined('__PUBLICAPP__') or define('__PUBLICAPP__', __ROOT__ . '/' . 'public/' . APP_NAME);
		//T-Team添加"__APPURL__"路径行为
		defined('__APPURL__') or define('__APPURL__', __ROOT__ . '/' . 'apps/' . APP_NAME . '/view/assets');
		
		require(BASE_PATH . 'base/extend/function.php');
		spl_autoload_register( 'autoload' );
		
		$controller = CONTROLLER_NAME . 'Controller';
		$action = ACTION_NAME;

		if( !class_exists($controller) ) {
			throw new Exception(APP_NAME. '/' .$controller.'.php 控制器类不存在', 404);
		}
		$obj = new $controller();
		
		if( !method_exists($obj, $action) ){
			throw new Exception(APP_NAME. '/' .$controller.'.php的' . $action.'() 方法不存在', 404);
		}
		$obj ->$action();

	} catch( Exception $e){
		cpError::show( $e->getMessage(), $e->getCode());
	}
}

run(); 