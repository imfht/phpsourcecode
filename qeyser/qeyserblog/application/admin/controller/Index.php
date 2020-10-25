<?php
namespace app\admin\controller;
use app\admin\controller\Base;

/**.-------------------------------------------------------------------
 * |    Software: [QBlog]
 * |    Site: www.qeyser.net
 * |-------------------------------------------------------------------
 * |    Author: 凯萨尔 <125790757@qq.com>
 * |    WeChat: 15999230034
 * |    Copyright (c) 2016-2018, www.qeyser.net. All Rights Reserved.
 * '-------------------------------------------------------------------*/

class Index extends Base{

	function index(){
		return $this->fetch();
	}

	function main(){
		return $this->fetch();
	}
}