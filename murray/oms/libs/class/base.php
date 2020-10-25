<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 基类
* 抽象类
*/

defined('INPOP') or exit('Access Denied');

abstract class Base{

	//构造函数
	protected function __construct(){}

	//加载类 
	static function Create($className){
		if(empty($className)){
			return null;
		}else{
			return new $className;
		}
	}
	
	//加载外挂 
	static function Plugin($className){
		if(empty($className)){
			return null;
		}else{
			return new $className;
		}
	}

	//加载服务 
	static function Service($className){
		if(empty($className)){
			return null;
		}else{
			return new $className."Service";
		}
	}

	//返回配置
	public function Config($parameter){
		require_once(PATH_CONFIG.DS.'config'.EXT);
		return CONFIG::Ini()->parameter;
	}

}
?>