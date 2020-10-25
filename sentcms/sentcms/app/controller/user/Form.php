<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\user;

use app\model\Member;

/**
 * @title 表单管理
 */
class Form extends Base {

	/**
	 * @title 表单数据列表
	 * @return [type] [description]
	 */
	public function index(){
		return $this->fetch();
	}
}