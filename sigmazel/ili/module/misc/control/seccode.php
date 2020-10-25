<?php
//版权所有(C) 2014 www.ilinei.com

namespace misc\control;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//验证码
class seccode{
	//默认
	public function index(){
		$seccode = new \ilinei\seccode('3', 80, 30);
		$random = $seccode->random();
		
		$_SESSION['_seccode'] = $random;
		
		$seccode->display();
	}
}
?>