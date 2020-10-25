<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 控制器抽象类
*/

defined('INPOP') or exit('Access Denied');

abstract class Control {

	public $view;
	public $_log;
	
	//构造函数
	protected function __construct(){
		if(DB_SESSION > 0){
			//启用数据库session
			$this->_session = Base::Create('session');
			$this->_session->open();
		}else{
			session_start();
		}
		$this->_log = Base::Create('log');
		$this->view = Base::Create('view');
		$this->_template = Base::Create('template');
	}
	
	//分发
	public static function dispatch($router){
		$control = $router['control'].'Control';		
		$action = $router['action'].'Action';
		$_control = new $control;
		$_control->view->controlName = $router['control'];
		$_control->view->actionName = $router['action'];
		//默认先执行_init()，初始化
		$_control->_init();
		//然后执行对应动作
		$_control->$action();
	}
	
	//渲染
	public function render($router){
		//获取模板信息
		$classDir_array = getClassDir($router['control']);
		$classDir_array['actionName'] = $router['action'];
		return $this->view->show($classDir_array);
	}
	
	//模板渲染
	public function tpl($templateid){
		$templateid = (int)$templateid;
		if($templateid < 1) return false;
		//获取模板数据
		$templateInfo = $this->_template->getOne($templateid);
		$viewCachePath = CACHE_VIEW_PATH.DS.'templates'.DS.$templateid.EXT;
		return $this->view->cache($templateInfo['content'], $viewCachePath);
	}
}
?>