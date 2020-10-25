<?php

namespace Api\Controller;

use Common\Controller\AdminbaseController;

class IndexController extends AdminbaseController {

	public function _initialize() {
		
	}

	/**
	 * 显示验证码
	 * @param type $length 验证码的长度
	 * @param type $height 图片的高度
	 * @param type $fontSize 文字的大小
	 * @param type $useCurve 
	 * @param type $fonttf 字体
	 * @echo Image
	 */
	public function show_verify() {
		$verify = new \Think\Verify(array(
			'length'	 => I('get.length', 4),
			'imageH'	 => I('get.height', 50),
			'imageW'	 => I('get.width', 238),
			'fontSize'	 => I('get.size', 20),
			'useCurve'	 => FALSE,
			'fontttf'	 => '5.ttf',
		));
		$verify->entry(1);
	}

}
