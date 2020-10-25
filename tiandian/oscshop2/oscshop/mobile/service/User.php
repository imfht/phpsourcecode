<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */ 
namespace osc\mobile\service;
use think\Db;
//用户数据
class User{
	
	//设置购物车商品数量
	function set_cart_total($total){
		session('mobile_total',$total);
	}
	
	function is_login(){
		
		$user=cookie('mobile_user_info');
		
	    if (empty($user)) {
	        return null;
	    } else {
	        return $user['uid'];
	    }
		
	}
	
}
?>