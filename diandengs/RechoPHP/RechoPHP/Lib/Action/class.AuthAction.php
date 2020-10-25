<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

class AuthAction extends RcAction {
	public function __construct(){

	}
	
	//-- 验证码验证 --
	public function verify(){
		if( M('Auth')->verification( $_POST['verify'])){
			echo '1';
		}
		else{
			exit('0');
		}
	}
	
	//-- 输出验证码 --
	public function putVerify(){
		M('Auth')->putVerify();
	}
}
