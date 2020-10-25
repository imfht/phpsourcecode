<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
* 空操作控制器
* @author molong <molong@tensent.cn>
*/
class EmptyController extends \Common\Controller\FrontController {

	public function _empty(){
		//模型入口
		$model = D('Model')->field('name')->where(array('extend'=>array('IN',array(1,2))))->select();
		foreach ($model as $key => $value) {
			$model_list[] = strtolower($value['name']);
		}
		if (in_array(strtolower(CONTROLLER_NAME), $model_list)) {
			$controller = A('Content');
			$action = ACTION_NAME;
			$controller->$action();
		}else{
			$this->error("非法操作！",U('Home/Index/index'));
		}
	}
}