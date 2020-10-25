<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 前台类
*/

defined('INPOP') or exit('Access Denied');

class Frontend extends Control {

	public static $_instance;
	public $_router;
	public $_setting;
	public $_user;
	public $_app;
	public $_acl;
	
	//启动
	public static function Run(){
		self::getInstance();
		self::setControlDir();
		self::dispatch();
	}
		
	protected function __construct(){
		parent::__construct();
		$this->_router = Router::get();
		$this->_setting = Base::Create('setting');
		$this->_user = Base::Create('user');
		$this->_app = Base::Create('app');
		$this->_acl = Base::Create('acl');
	}

	//实例化(单例模式)
    public static function getInstance(){
        if(null === self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }
	
	//设置目录
	public static function setControlDir(){}
	
	//获取目录
	public static function getControlDir(){}
	
	//分发
	public static function dispatch(){
		self::getInstance();
		//附加用户信息
		if(!$_SESSION['user']){
			self::$_instance->_user->checkLogin();
		}
		//定义跳转地址
		$loginControl = "frontend_do";
		$loginAction = "login";
		//执行权限检测
		$canDispatch = self::$_instance->_acl->verity(self::$_instance->_router, self::$_instance->_user);
		if(!$canDispatch){
			if($_SESSION['user']){
				print_r($canDispatch);
				exit("no access");
			}else{
				$aclArray = self::$_instance->_router;
				if(($aclArray['control'] !== $loginControl) || ($aclArray['action'] !== $loginAction)){
					header('Location: '.SELF_PATH.'do/login/');
					exit;			
				}
			}
		}
		parent::dispatch(self::$_instance->_router);
	}
	
	//文件渲染
	public function render(){
		//获取模板信息
		parent::render(self::$_instance->_router);
	}
	
	//默认初始化
	public function _init(){
		$this->view->user = $_SESSION['user'];
		$this->view->app = $_SESSION['app'];
	}
	
}
