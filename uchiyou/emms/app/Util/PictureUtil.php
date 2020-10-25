<?php
namespace App\Util;

class PictureUtil{
	
	public static function getPicBasePath(){
		return '/picture/';
	}
	/*
	 * 从浏览器传递过来的图片无法显示   '.'  以后的后缀名称,所以用此方法替换字符
	 */
	public static function removeDot($fileName){
		return str_replace('.','_',$fileName);
	}
	public static function recoverDot($fileName){
		return str_replace('_','.',$fileName);
	}
	

}