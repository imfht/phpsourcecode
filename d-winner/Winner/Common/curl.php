<?php
/*
 * @varsion		EasyWork系统 1.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */
 
//curl获取远程数据 需要开启curl库
/*
$url		远程地址
*/
function curl_get_contents($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);            		//设置访问的url地址
	//curl_setopt($ch,CURLOPT_HEADER,1);            		//是否显示头部信息
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);           		//设置超时
	curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);   	//用户访问代理 User-Agent
	curl_setopt($ch, CURLOPT_REFERER,_REFERER_);        	//设置 referer
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);      		//跟踪301
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);       		 //返回结果
	$r = curl_exec($ch);
	curl_close($ch);
	return $r;
}