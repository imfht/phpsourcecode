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

/**
 * Auth class
 * $Author: Recho $license: http://www.recho.net/ $
 * $create time: 2011-11-01 18:50
 * $last update time: 2011-11-02 18:50 Recho $
 */
defined('IS_IN') or die('Include Error!');
class Auth extends RcModel{
	private $secureStr='jackcong~520haha~3';
	
	/**
	 * 验证码验证
	 * @param unknown_type $verify
	 */
	public function verification( $verify){
		@session_start();
		if( md5($this->secureStr.strtolower($verify))==$_SESSION['verify']){
			return true;
		}
		return false;
	}
	
	/**
	 * 输出验证码
	 */
	public function putVerify(){
		include_once( RC_PATH_LIB.'class.Checkcode.php');
		$aFonts = array(
			0 => 'Aardvark_Cafe.ttf',
			1 => 'Aardvark_Cafe.ttf',
		);
		new checkCode(4,$aFonts[mt_rand(0,1)], 'verify', NULL, $this->secureStr);
	}
	
	/**
	 * 清除验证码
	 */
	public function unsetVerify(){
		unset($_SESSION['verify']);
	}
}