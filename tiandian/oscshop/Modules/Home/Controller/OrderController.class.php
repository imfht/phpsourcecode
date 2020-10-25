<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Home\Controller;

class OrderController extends CommonController {
	
	protected function _initialize(){
		parent::_initialize();
		 // 获取当前用户ID
        define('UID',is_login());
        if(!UID){
            $this->error('请先登录');
        }
	}
	
	function index(){
	
	}
	
}