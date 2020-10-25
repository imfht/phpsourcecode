<?php
namespace addons\demo\controller;

use muucmf\addons\Admin as AddonsAdmin;

/**
 *插件管理类 
**/

class Admin extends AddonsAdmin
{

	public function index(){

		
		$this->setTitle('插件管理后台');
		return $this->fetch();
	}

	public function test(){

		
		$this->setTitle('插件管理后台');
		$test = 'This is a TEST';
		$this->assign('test',$test);
		return $this->fetch();
	}

}