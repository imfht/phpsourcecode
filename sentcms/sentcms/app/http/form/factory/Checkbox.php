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
class Checkbox extends \app\http\form\Factory {

	public function show(){
		return $this->display('checkbox');
	}

	protected function parseValue(){
		$this->field['value'] = isset($this->data[$this->field['name']]) ? $this->data[$this->field['name']] : [];
	}

}