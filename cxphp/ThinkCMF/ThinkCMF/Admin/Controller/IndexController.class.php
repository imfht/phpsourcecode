<?php

/**
 * 后台首页
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class IndexController extends AdminbaseController {

	function _initialize() {
		$this->initMenu();
		parent::_initialize();
	}

	//后台框架首页
	public function index() {
		$this->assign("SUBMENU_CONFIG", json_encode(D("Menu")->menu_json()));
		$this->display();
	}

}
