<?php
/**
 * Printemps Framework Router Dispatch System
 * Printemps Framework 路由分发系统
 * 2015 Printemps Framework DevTeam
 */
class Printemps_Router{
	/**
	 * 路由分发函数：需要修改 config.inc.php 确定模式
	 * @type static
	 * ------------------------------------
	 * Printemps的路由分发有三种方法：QUERY_STING模式，PATHINFO模式和伪静态模式
	 * 默认是QUERY_STRING模式，格式为：http://localhost/?控制器名称/方法名称/参数1/参数1的值/...
	 * PATHINFO模式：格式为：http://localhost/index.php/控制器名称/方法名称/参数1/参数1的值/...
	 * 伪静态模式：格式为：http://localhost/控制器名称/方法名称/参数1/参数1的值/...
	 */
	public static function Dispatch(){
		$dispatchMode = APP_ENTRY_MODE;			//获取路由分发模式

		switch($dispatchMode){
			/** QUERY_STRING 模式 */
			case 1:
			$request = $_SERVER['QUERY_STRING'];
			break;
			/** PATH_INFO 模式 */
			case 2:
			case 3:
			isset($_SERVER['PATH_INFO']) ? $request = $_SERVER['PATH_INFO'] : $request = '';
			break;
		}
		$requestArray = explode("/",$request);
		isset($requestArray[1]) ? $controllerName = $requestArray[1] : $controllerName=$functionName = '';
		isset($requestArray[2]) ? empty($requestArray[2]) ? $functionName = 'index'  : $functionName = $requestArray[2] : $functionName = 'index';

		self::getParam($requestArray);
		self::loadController($controllerName,$functionName);
	}

	/**
	 * 获取随着请求地址传入的参数，返回到 $param 变量中
	 * @param  array $requestArray 路由处理后的请求数组
	 * @return array               返回处理后的数组
	 */
	static function getParam($requestArray){
		global $param;
		$param = array();
		if(isset($requestArray[3])){
			$pt = 2;
			while(isset($requestArray[$pt])){
				isset($requestArray[$pt+1])? $paramValue = $requestArray[$pt+1] : array_push($param,$requestArray[$pt]);
				if(isset($paramValue))
					$param[$requestArray[$pt]] = $paramValue;
				$pt = $pt+2;
				unset($paramValue);
			}
		}
	}

	/**
	 * 加载控制器：class格式为控制器名+Controller，如indexController
	 * Controller 最好继承 Printemps，已获得最佳性能
	 * @type static
	 * @param  string $className  要加载的控制器名，默认加载index
	 * @param  string $methodName 要加载的方法名，默认加载run
	 * @param  array or null $param      通过URL路由传入的参数
	 * @return none             
	 */
	static function loadController($className , $methodName){
		global $param;
		empty($className) ? $controllerName = 'index' : $controllerName = $className;
		if(!class_exists($controllerName.'Controller')){
			if(file_exists(APP_CORE_PATH.'Controller/'.$controllerName.'.class.php')){
				require APP_CORE_PATH.'Controller/'.$controllerName.'.class.php';
				if(!class_exists($controllerName.'Controller'))
					Printemps_Error(500,"啊嘞，通过路由地址请求的控制器 {$controllerName} 不存在哦 :)");
			}
			else{
				Printemps_Error(500,"啊嘞，通过路由地址请求的控制器 {$controllerName} 不存在哦 :)");
			}
		}
		$Controller = $controllerName.'Controller';
		$cl = new $Controller();

		$functionName = $methodName;
		if(method_exists($cl, $functionName))
			$cl->$functionName();
		else
			Printemps_Error(500,'啊嘞，通过路由地址请求的方法 {$functionName} 不存在哦 :)');

	}

}