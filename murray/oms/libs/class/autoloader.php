<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 自动加载类
*/

defined('INPOP') or exit('Access Denied');

class AutoLoader{
	
	protected static $_instance;
	//私有初始化
    protected function __construct(){
        spl_autoload_register(array($this, 'Loader'));
    }	
	
	//加载类文件
	private function Loader($className){
		$classPath = BASE_PATH.DS;
		$array_classPath = getClassDir($className);
		if($array_classPath['path']) $classPath .= $array_classPath['path'].DS;
		if(strpos($array_classPath['className'], 'Control')){
			//针对控制器
			$classPath .= CONTROL_PATH.DS;
			$classPath = strtolower($classPath);
			$classPath .= $array_classPath['className'].EXT;
		}elseif(strpos($array_classPath['className'], 'Model')){
			//针对模型
			$classPath .= MODEL_PATH.DS;
			$classPath = strtolower($classPath);
			$classPath .= $array_classPath['className'].EXT;
		}elseif(strpos($array_classPath['className'], 'Weiget')){
			//针对组件
			$classPath .= WEIGET_PATH.DS;
			$classPath = strtolower($classPath);
			$classPath .= $array_classPath['className'].EXT;
		}elseif(strpos($array_classPath['className'], 'Plugin')){
			//针对外挂
			$classPath = PLUGIN_PATH.DS;
			$classPath = strtolower($classPath);
			$classPath .= $array_classPath['className'].EXT;
		}elseif(strpos($array_classPath['className'], 'Service')){
			//针对服务
			$classPath = SERVICE_PATH.DS;
			$classPath = strtolower($classPath);
			$classPath .= str_replace("Service", "", $array_classPath['className']).DS;
			$classPath .= $array_classPath['className'].EXT;
		}else{
			//系统库路径，特殊处理
			$classPath = CLASS_PATH.DS;
			$classPath .= $array_classPath['className'].EXT;
			$classPath = strtolower($classPath);
		}
		include $classPath;
	}
	
	//实例化
    public static function getInstance(){
        if(null === self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }
	
}