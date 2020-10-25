<?php

/**
 * Created by PhpStorm.
 * User: William
 * Date: 2016/9/12
 * Time: 19:50
 */
class Curl
{
	public $cookie = '';

	/**
	 * request 执行一次curl请求
	 * @param  string $method 请求方法
	 * @param  string $url 请求的URL
	 * @param  array $fields 执行POST请求时的数据
	 * @return string           请求结果
	 */
	public function request($method, $url, $header = '', array $fields = array())
	{
		//$cookiePath = self::initCookie($url);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch,CURLOPT_TIMEOUT ,20 );
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		if (strtoupper($method) == 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		}
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');//加入GZIP解析，这一个很重要
		if ($this->cookie) {
			curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
		}
		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpCode == 404 || $httpCode == 403 || $httpCode == 500) {
			return NULL;
		}
		return $result;
	}

	/**
	 * 初始化cookie
	 * @return string   cookie存放路径
	 */
	public function initCookie($url)
	{
		$curl = curl_init();//初始化curl模块
		curl_setopt($curl, CURLOPT_URL, $url);//登录提交的地址
		curl_setopt($curl, CURLOPT_HEADER, 1);//是否显示头信息
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息
		curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIEPATH); //设置Cookie信息保存在指定的文件中
		curl_exec($curl);//执行cURL
		curl_close($curl);//关闭cURL资源，并且释放系统资源
	}

	/**
	 * 设置cookie用于获取操作权限
	 * @param string $cookie
	 */
	public function setCookie($cookie)
	{
		$this->cookie = $cookie;
	}
}