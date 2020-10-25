<?php
namespace app\index\controller;
use app\index\controller\Base;

/**.-------------------------------------------------------------------
 * |    Software: [QeyserBlog]
 * |    Site: www.qeyser.net
 * |-------------------------------------------------------------------
 * |    Author: 凯撒 <125790757@qq.com>
 * |    WeChat: 15999230034
 * |    Copyright (c) 2017-2018, www.qeyser.net . All Rights Reserved.
 * '-------------------------------------------------------------------*/

class Friendlink extends Base{
	/**
	 * 友情链接首页
	 */
	public function index(){
		// 查询友情链接
		$data = db('links')->select();
		$this->assign('data',$data);
		// 显示模板
		return $this->fetch();
	}

}