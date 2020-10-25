<?php
class Validate
{


	public static /**/function _validataEmail($domain){
		$pattern_test = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
		return  preg_match($pattern_test,$domain);
	}

	public static function GetIP(){
		if(!empty($_SERVER["HTTP_CLIENT_IP"])){
			$cip = $_SERVER["HTTP_CLIENT_IP"];
		}
		elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
			$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		elseif(!empty($_SERVER["REMOTE_ADDR"])){
			$cip = $_SERVER["REMOTE_ADDR"];
		}
		else{
			$cip = "无法获取！";
		}
		return $cip;
	}
}