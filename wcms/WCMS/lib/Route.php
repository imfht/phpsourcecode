<?php
/**
 * 自定义请求容器Zend_Controller_Request_Http
 * @author Wolf [Email: 116311316@qq.com]
 * @link http://www.seven66.com
 * @since 2010-10-20
 * @version 1.0
 */
class Route {
	
	protected $_requestUri; //解析地址栏
	

	//这些原本在Abstract接口中的 你可以直接拿过来
	protected $_dispatched = false;
	protected $_currentModule = '';
	protected $_defaultController = '';
	protected $_defaultAction = '';
	protected $_controller = '';
	protected $_action = '';
	protected $config; //模块域名配置
	

	/**
	 * 初始化 得到RquestUri
	 */
	public function __construct() {
		$this->config ();
	}
	/**
	 * 获取默认模块配置信息
	 */
	public function config() {
		$this->config = require 'bootstrap.php';
		
		! empty ( $this->config ['default'] ['controller'] ) && $this->_defaultController = $this->config ['default'] ['controller'];
		! empty ( $this->config ['default'] ['action'] ) && $this->_defaultAction = $this->config ['default'] ['action'];
	}
	
	/**   
	 * @return string 请求的完整 URL
	 */
	public function requestUri() {
		$uri = $_SERVER ['REQUEST_URI'];
		//加载域名配置 首页为静态文件  多域名绑定 这个不是跳转
		$domain = include_once 'domain.php';
		
		$categoryid = array_search ( $_SERVER ['HTTP_HOST'], $domain );
		
		if ($categoryid > 0 && $uri == '/') {
			$uri = '/news/c/?cid=' . $categoryid;
			$_GET ['cid'] = $categoryid;
		}
		
		$this->_requestUri = $uri;
		
		return $uri;
	}
	
	/**
	 * 解析路由 默认路由协议index/index/?id=1
	 * @tudo 是否使用htaccess index.php判断未做
	 * @param string $url
	 */
	public function paresUri() {
		$url = $this->requestUri ();
		//三种情况 1只有域名 2只有控制器和方法 3带有参数情况
		

		$default = false;
		if (strpos ( $url, "?" )) {
			//如果开启了隐藏index.php 取消下面的这句代码
			$url = preg_replace ( "#.+index\.php\?#i", "/", $url );
			
			preg_match ( "#^\/([a-z]+)\/([a-z]+)\/\?(.+)#", $url, $string );
			
			$allow = preg_match ( "#['\(\)\*<>]+#", $string [3], $hack );
			
			if ($allow) {
				echo "参数不正确,安全小助手发现了不允许的词";
				exit();
				return;
			
			}
		
		} elseif ($url == "/") {
		
			//配置默认路由
			$this->setControllerName ( $this->_defaultController );
			$this->setActionName ( $this->_defaultAction );
			$default = TRUE;
		} 
		
		$route = array ();
		if (! $default) {
			$allow = preg_match ( "#^/([a-z]+)\/([a-z]+)#", $url, $route );
			
			if (! $allow) {
				header ( "HTTP/1.1 404 Not Found" );
			}
			
			$this->setControllerName ( $route [1] );
			$this->setActionName ( $route [2] );
		}
		//重新封装GET
		if (strpos ( $url, "=" )) {
			$params = explode ( "/?", $url );
			if (strpos ( $url, "&" )) {
				$arr = explode ( "&", $params [1] );
				
				$this->parseGET ( $arr );
			} else {
				$this->parseGET ( $params [1] );
			}
		}
		// 清除变量
		unset ( $_ENV, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_ENV_VARS );
		
		// 禁止对全局变量注入
		isset ( $_REQUEST ['GLOBALS'] ) or isset ( $_FILES ['GLOBALS'] ) && exit ( 'Request tainting attempted.' );
	
	}
	
	//处理GET参数
	private function parseGET($str) {
		
		if (is_array ( $str )) {
			foreach ( $str as $v ) {
				preg_match ( "#(.*)=(.*)#", $v, $rs );
				$get [$rs [1]] = $rs [2];
			}
		} else {
			preg_match ( "#(.*)=(.*)#", $str, $rs );
			$get [$rs [1]] = $rs [2];
		}
		$_REQUEST = $get;
		$_GET = $get;
	
	}
	
	/**
	 * 填充
	 * @param string $key
	 */
	public function setModuleName($value) {
		return $this->_currentModule = $value;
	}
	/**
	 * 填充
	 * @param string $key
	 */
	public function setControllerName($value) {
		return $this->_controller = $value;
	}
	/**
	 * 填充
	 * @param string $key
	 */
	public function setActionName($value) {
		return $this->_action = $value;
	}
	/**
	 * @return string
	 */
	public function getModuleName() {
		return $this->_currentModule;
	}
	/**
	 * @return string
	 */
	public function getControllerName() {
		return $this->_controller;
	}
	/**
	 * @return string
	 */
	public function getActionName() {
		return $this->_action;
	}
	/**
	 * Get the HTTP host.
	 *
	 * "Host" ":" host [ ":" port ] ; Section 3.2.2
	 * Note the HTTP Host header is not the same as the URI host.
	 * It includes the port while the URI host doesn't.
	 *
	 * @return string
	 */
	public function getHttpHost() {
		return $_SERVER ['HTTP_HOST'];
	
	}
}