<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * 使用IP类必须开启curl扩展
 * 默认使用新浪IP库
 * @category   ORG
 * @package  ORG
 * @subpackage  Net
 * @author    liu21st <liu21st@gmail.com>
 */
class IpLocation {
	public $ip_lib = 'taobao';											//ip库提供腾讯和淘宝，‘tencent’、‘taobao’
	
	
	//获取ip地址的详细信息
	public function getIpAddr($ip){
		if($this->ip_lib=='tencent'){
			$url = 'http://ip.qq.com/cgi-bin/searchip?searchip='.$ip;
		}else{
			$url = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
		}
		$ch = curl_init($url); 
    	curl_setopt($ch,CURLOPT_ENCODING ,'gb2312'); 				//ip库编码
    	curl_setopt($ch, CURLOPT_TIMEOUT, 10); 						//超时中断时间
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; 			// 获取数据返回
		$result = curl_exec($ch); 
		$result = mb_convert_encoding($result, "utf-8", "gb2312");
		if($this->ip_lib=='tencent'){
			preg_match("/<span>(.*?)<\/span><\/p>/iU",$result,$ipArray); 
   			$loc = $ipArray[1]; 
   			return str_replace("&nbsp;","",$result);
		}else{
			return json_decode($result,true);
		}
		curl_close($ch); 
	}
	
	//获取真实ip地址
	function getIp(){
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
			$ip = getenv("HTTP_CLIENT_IP"); 
		}else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
			$ip = getenv("HTTP_X_FORWARDED_FOR"); 
		}else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
			$ip = getenv("REMOTE_ADDR"); 
		}else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
			$ip = $_SERVER['REMOTE_ADDR']; 
		}else {
			$ip = "unknown"; 
		}
		return($ip);
	}
}