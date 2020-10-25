<?php 
/*
 * @varsion		EasyWork系统 1.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */

//获取中文拼音首字符
function getfirstchar($s0) {
$fchar = ord($s0 {
0 });
if ($fchar >= ord("A") and $fchar <= ord("z"))
return strtoupper($s0 {
0 });
$s1 = iconv('UTF-8','GB2312//ignore',$s0);
$s2 = iconv("GB2312", "UTF-8", $s1);
if ($s2 == $s0) {
$s = $s1;
} else {
$s = $s0;
}
$asc = ord($s {
0 }) * 256 + ord($s {
1 }) - 65536;
if ($asc >= -20319 and $asc <= -20284)
return "A";
if ($asc >= -20283 and $asc <= -19776)
return "B";
if ($asc >= -19775 and $asc <= -19219)
return "C";
if ($asc >= -19218 and $asc <= -18711)
return "D";
if ($asc >= -18710 and $asc <= -18527)
return "E";
if ($asc >= -18526 and $asc <= -18240)
return "F";
if ($asc >= -18239 and $asc <= -17923)
return "G";
if ($asc >= -17922 and $asc <= -17418)
return "H";
if ($asc >= -17417 and $asc <= -16475)
return "J";
if ($asc >= -16474 and $asc <= -16213)
return "K";
if ($asc >= -16212 and $asc <= -15641)
return "L";
if ($asc >= -15640 and $asc <= -15166)
return "M";
if ($asc >= -15165 and $asc <= -14923)
return "N";
if ($asc >= -14922 and $asc <= -14915)
return "O";
if ($asc >= -14914 and $asc <= -14631)
return "P";
if ($asc >= -14630 and $asc <= -14150)
return "Q";
if ($asc >= -14149 and $asc <= -14091)
return "R";
if ($asc >= -14090 and $asc <= -13319)
return "S";
if ($asc >= -13318 and $asc <= -12839)
return "T";
if ($asc >= -12838 and $asc <= -12557)
return "W";
if ($asc >= -12556 and $asc <= -11848)
return "X";
if ($asc >= -11847 and $asc <= -11056)
return "Y";
if ($asc >= -11055 and $asc <= -10247)
return "Z";
return null;
}

//以上函数返回单个汉字的拼音首字母。
//当需要处理中文字符串时，只需要重新写一个函数，用来取得一串汉字的拼音首字母。
function firstPinyin($zh) {
	$ret = "";
	$s1 = iconv('UTF-8', 'GB2312//ignore', $zh);
	$s2 = iconv("GB2312", "UTF-8", $s1);
	if ($s2 == $zh) {
		$zh = $s1;
	}
	for ($i = 0; $i < strlen($zh); $i++) {
		$s1 = substr($zh, $i, 1);
		$p = ord($s1);
		if ($p > 160) {
			$s2 = substr($zh, $i++, 2);
			$ret .= getfirstchar($s2);
		} else {
			$ret .= strtoupper($s1);
		}
	}
	return strtoupper($ret);
	$ret = '';
}