<?php

//统计类
class StatisticsAction extends GlobalAction {

	public static function addRecord(){

		//正在浏览当前页面用户的 IP 地址
		$ip = $_SERVER['REMOTE_ADDR'];
		
		//正在浏览当前页面用户的主机名
		$host = $_SERVER['REMOTE_HOST'];
		
		//用户连接到服务器时所使用的端口
		$port = $_SERVER['REMOTE_PORT'];
		
		//用户使用的浏览器信息
		$brower = $_SERVER['HTTP_USER_AGENT'];

		//当前请求头中 Accept
		$accept = $_SERVER['HTTP_ACCEPT'];

		//当前请求头中 Accept-Charset
		$charset = $_SERVER['HTTP_ACCEPT_CHARSET'];

		//当前请求头中 Accept-Encoding
		$encoding = $_SERVER['HTTP_ACCEPT_ENCODING'];

		//用户使用的浏览器语言
		$language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		
		//访问页面使用的请求方法
		$method = $_SERVER['REQUEST_METHOD'];
		
		//链接到当前页面的前一页面的 URL 地址
		$from = $_SERVER['HTTP_REFERER'];
		
		//访问此页面所需的 URI
		$go = $_SERVER['REQUEST_URI'];
		
		//请求开始时的时间
		$time = Date('Y-m-d H:i:s');
		
		$sta = D('statistics');
		
		$sta -> add(array('ip' => $ip, 'host' => $host, 'port' => $port, 'brower' => $brower, 'accept' => $accept, 'charset' => $charset, 'encoding' => $encoding, 'language' => $language, 'method' => $method, 'from' => $from, 'go' => $go, 'time' => $time));

	}

}