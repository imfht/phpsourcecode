<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\http\form;

use think\facade\View;

/**
 * @title 后台中间件
 */
class Form {

	public static function render($field, $data){
		if (in_array($field['type'], ['string', 'text'])) {
			$field['type'] = 'text';
		}

		$class = "app\\http\\form\\factory\\" . ucfirst($field['type']);

		if (class_exists($class)) {
			$elem = new $class($field, $data);
		}else{
			$elem = new \app\http\form\Factory($field, $data);
		}
		
		return $elem->show();
	}
}