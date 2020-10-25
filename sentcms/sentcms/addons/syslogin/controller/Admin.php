<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace addons\syslogin\controller;

class Admin extends \app\controller\admin\Base{
	
	/**
	 * @title 第三方登录设置
	 */
    public function setting(){
		$this->data = [
			'meta_title' => '第三方登录设置',
		];
		return $this->fetch();
    }
}
