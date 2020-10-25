<?php
/*
 * @varsion		EasyWork系统 1.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */
 
//数组编码转换函数
/*
return Array
$in_charset		原字符串编码
$out_charset	输出字符串编码
$arr			传入的数组
*/
function array_iconv($in_charset,$out_charset=NULL,$arr){ 
	if($out_charset){
		$str = '$resArr = '.iconv($in_charset,$out_charset.'//IGNORE',var_export($arr,true)).' ;';
		eval($str);
	}else{
		$str = '$resArr = '.auto_iconv($in_charset,var_export($arr,true)).' ;';
		eval($str);
	}
	return $resArr;
}  

//转码
/*
return Sstr
$str		传入的字符串
*/
function auto_iconv($in_charset,$str){
	$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
	if (preg_match("/zh-c/i", $lang)){
		return iconv($in_charset,"GBK//IGNORE",$str);
	}else if (preg_match("/zh/i", $lang)){
		return iconv($in_charset,"Big5//IGNORE",$str);
	}else if (preg_match("/en/i", $lang)){
		return $str;
	}
}