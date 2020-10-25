<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

class IndexAction extends RcAction{
	
	public function __construct(){
		parent::__construct();
	}
	
	//-- 首页 --
	public function index(){
		$this->display();
	}
}
