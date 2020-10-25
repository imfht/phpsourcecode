<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 组件类
*/

defined('INPOP') or exit('Access Denied');

class Weiget extends Control {

	public static $_instance;
	public $_router;
	
	//启动
	public static function get($name){
		self::getInstance();
		self::$_instance->_router = Router::analysis($name);
		//返回路径，用于引入
		return self::dispatch($name);
	}
		
	public function __construct(){
		parent::__construct();
		$this->_router = Router::get();
		$this->_setting = Base::Create('setting');
		$this->_user = Base::Create('user');
		$this->_app = Base::Create('app');
		$this->_acl = Base::Create('acl');
	}
	
	//实例化(单例模式)
    private static function getInstance(){
        if(null === self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }
	
	//分发
	public static function dispatch($name){
		self::getInstance();
		if(!self::$_instance->_router) return false;
		parent::dispatch(self::$_instance->_router);
	}
	
	//渲染
	public function render(){
		//获取模板信息
		parent::render(self::$_instance->_router);
	}

	//默认初始化
	public function _init(){}
	
}
