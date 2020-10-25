<?php

/**
 * 首页
 */

namespace Portal\Controller;

use Common\Controller\HomeBaseController;

class IndexController extends HomeBaseController {

	//首页
	public function index() {
		$this->display("Portal:index");
	}

}
