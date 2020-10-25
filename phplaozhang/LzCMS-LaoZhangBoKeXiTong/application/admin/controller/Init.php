<?php
namespace app\admin\controller;

use think\Controller;

/**
* admin模块基础类
*/
class Init extends Controller
{
	
	function _initialize()
	{
		parent::_initialize();
		error_reporting(0);
		$this->model = model('admin/admin');
		if(!session('?admin_user') && strtolower(request()->controller()) != 'login'){
			$this->redirect('login/login');
		}
		$this->settings = cache('settings');
		// 发送基本信息
		$this->assign(['settings' => $this->settings,'admin_user' => session('admin_user')]);
	}


}