<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\front;

use QL\QueryList;

class Index extends Base {
	
	/**
	 * @title 网站首页
	 * @return [type] [description]
	 */
	public function index() {
		$this->setSeo("网站首页", '网站首页', '网站首页');
		return $this->fetch();
	}

	/**
	 * @title miss
	 * @return [type] [description]
	 */
	public function miss(){
		return $this->fetch();
	}
}
