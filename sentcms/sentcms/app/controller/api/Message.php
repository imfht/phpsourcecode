<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\api;

use think\facade\Db;

/**
 * @title 消息管理
 */
class Message extends Base {

	/**
	 * @title 消息列表
	 */
	public function index(){
		$this->data['code'] = 1;
		return $this->data;
	}

	/**
	 * @title 通知列表
	 */
	public function notice(){
		$this->data['code'] = 1;
		return $this->data;
	}
}