<?php
namespace App\Util;

class CheckCode{
	
	public static function getCode(){
		return mt_rand(10000,99999);
	}
}