<?php 
namespace Admin\Controller;
use Common\Controller\BaseController;

/**
 * Admin模块公共控制器
 */
 class CommonController extends BaseController
{
	protected function _initialize()
	{
		if( $this->_checkLogin() ){ //已登录或可以cookie登录
			C('breadcrumb', array());
			$this->loadSettings(); //加载系统配置信息到C('settings')中
			$this->_checkExtendjs(); //加载每个页面独有的脚本文件
			$this->assign(session()); //模板变量填充账号信息
		}else{ //全新访问
			if ( __SELF__ != '/' ) { //当前在内容页面
				$this->error('抱歉，你尚未登录', '/Auth/login');
			}else{ //当前在首页面
				$this->redirect('/Auth/login');
			}
		}
	}

	/**
	 * 检查是否已登录
	 */
	private function _checkLogin()
	{
		if( session('authName') ){ //已经会话登录
			session('authName')==C('super_user')? define('IS_ROOT', true) : NULL;
			return true;
		}elseif( cookie('account') && cookie('password') ) { //未登录但cookie存在
			return R('Auth/loginTo', cookie('account'), cookie('password'));
		}else{ //全新访问
			return false;
		}
	}

	/**
	 * 加载每个页面独有的脚本文件
	 */
	private function _checkExtendjs()
	{
		$extendJs = strtolower(CONTROLLER_NAME).'-'.strtolower(ACTION_NAME).'.js';

		if( file_exists(__ROOT__.'Public/Js/Admin/'.$extendJs) ){
			$this->assign('extendJs', $extendJs);
		}else{
			$this->assign('extendJs', null);
		}
	}

	/**
	 * 追加一个面包屑导航项目
	 * @param string $name  导航项目名
	 * @param string $path  导航路径
	 */
	protected function bcItemPush($name, $path=''){
		$tmp = C('breadcrumb');
		$tmp[] = array('name'=>$name, 'path'=>$path);
		C('breadcrumb', $tmp);
		$this->assign('breadcrumb', $tmp);
	}

}
?>