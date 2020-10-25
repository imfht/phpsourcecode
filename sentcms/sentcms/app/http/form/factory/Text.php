<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\http\form\factory;

use think\facade\View;

/**
 * @title 后台中间件
 */
class Text extends \app\http\form\Factory {

	public function show(){
		return $this->display('text');
	}

}