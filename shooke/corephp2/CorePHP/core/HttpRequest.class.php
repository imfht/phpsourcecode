<?php
namespace Core;
/**
 * @author shooke
 * http请求处理类
 * 接收http请求各种处理,如ip地址，端口，$_GET $_POST $_COOKIE $_REQUEST $_SERVER等
 */
class HttpRequest{
	/**
	 * 访问的端口号
	 *
	 * @var int
	 */
	protected static $_port = null;
	/**
	 * 请求路径信息
	 *
	 * @var string
	 */
	protected static $_hostInfo = null;
	/**
	 * 客户端IP
	 *
	 * @var string
	 */
	protected static $_clientIp = null;
	
	/**
	 * 语言
	 *
	 * @var string
	 */
	protected static $_language = null;
	
	/**
	 * 路径信息
	 *
	 * @var string
	 */
	protected static $_pathInfo = null;	
	
	/**
	 * 请求脚本url
	 *
	 * @var string
	*/
	private static $_scriptUrl = null;
	
	/**
	 * 请求参数uri
	 *
	 * @var string
	 */
	private static $_requestUri = null;
	
	/**
	 * 基础路径信息
	 *
	 * @var string
	 */
	private static $_baseUrl = null;
	
	
	
	/**
	 * 初始化request对象
	 *
	 * 对输入参数做转义处理
	 */
	public static function normalizeRequest() {
		if (MAGIC_QUOTES_GPC) {
			if (isset($_GET)) $_GET = self::_stripSlashes($_GET);
			if (isset($_POST)) $_POST = self::_stripSlashes($_POST);
			if (isset($_REQUEST)) $_REQUEST = self::_stripSlashes($_REQUEST);
			if (isset($_COOKIE)) $_COOKIE = self::_stripSlashes($_COOKIE);
		}
	}
	
	
	/**
	 * 获得用户请求的数据
	 *
	 * 返回$_GET,$_POST的值,未设置则返回$defaultValue
	 * @param string $name 获取的参数name,默认为null将获得$_GET和$_POST两个数组的所有值
	 * @param mixed $defaultValue 当获取值失败的时候返回缺省值,默认值为null
	 * @return mixed
	 */
	public static function getRequest($name = null, $defaultValue = null) {
		return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $defaultValue;
	}
	
	/**
	 * 获取请求的表单数据
	 *
	 * 从$_POST获得值
	 * @param string $name 获取的变量名,默认为null,当为null的时候返回$_POST数组
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认为null
	 * @return mixed
	 */
	public static function getPost($name = null, $defaultValue = null) {
		if ($name === null) return $_POST;
		return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
	}
	
	/**
	 * 获得$_GET值
	 *
	 * @param string $name 待获取的变量名,默认为空字串,当该值为null的时候将返回$_GET数组
	 * @param string $defaultValue 当获取的变量不存在的时候返回该缺省值,默认值为null
	 * @return mixed
	 */
	public static function getGet($name = null, $defaultValue = null) {
		if ($name === null) return $_GET;
		return (isset($_GET[$name])) ? $_GET[$name] : $defaultValue;
	}
	
	/**
	 * 返回cookie的值
	 *
	 * 如果$name=null则返回所有Cookie值
	 * @param string $name 获取的变量名,如果该值为null则返回$_COOKIE数组,默认为null
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认该值为null
	 * @return mixed
	 */
	public static function getCookie($name = null, $defaultValue = null) {
		if ($name === null) return $_COOKIE;
		return (isset($_COOKIE[$name])) ? $_COOKIE[$name] : $defaultValue;
	}
	
	/**
	 * 返回session的值
	 *
	 * 如果$name=null则返回所有SESSION值
	 * @param string $name 获取的变量名,如果该值为null则返回$_SESSION数组,默认为null
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认该值为null
	 * @return mixed
	 */
	public static function getSession($name = null, $defaultValue = null) {
		if ($name === null) return $_SESSION;
		return (isset($_SESSION[$name])) ? $_SESSION[$name] : $defaultValue;
	}
	
	/**
	 * 返回Server的值
	 *
	 * 如果$name为空则返回所有Server的值
	 * @param string $name 获取的变量名,如果该值为null则返回$_SERVER数组,默认为null
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认该值为null
	 * @return mixed
	 */
	public static function getServer($name = null, $defaultValue = null) {
		if ($name === null) return $_SERVER;
		return (isset($_SERVER[$name])) ? $_SERVER[$name] : $defaultValue;
	}
	
	/**
	 * 返回ENV的值
	 *
	 * 如果$name为null则返回所有$_ENV的值
	 * @param string $name 获取的变量名,如果该值为null则返回$_ENV数组,默认为null
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认该值为null
	 * @return mixed
	 */
	public static function getEnv($name = null, $defaultValue = null) {
		if ($name === null) return $_ENV;
		return (isset($_ENV[$name])) ? $_ENV[$name] : $defaultValue;
	}
	
	/**
	 * 获取请求链接协议
	 *
	 * 如果是安全链接请求则返回https否则返回http
	 * @return string
	 */
	public static function getScheme() {
		return (self::getServer('HTTPS') == 'on') ? 'https' : 'http';
	}
	
	/**
	 * 返回请求页面时通信协议的名称和版本
	 * @return string
	 */
	public static function getProtocol() {
		return self::getServer('SERVER_PROTOCOL', 'HTTP/1.0');
	}
	
	/**
	 * 返回访问IP
	 *
	 * 如果获取请求IP失败,则返回0.0.0.0
	 * @return string
	 */
	public static function getClientIp() {
		if (!self::$_clientIp) self::_getClientIp();
		return self::$_clientIp;
	}
	
	/**
	 * 获得请求的方法
	 *
	 * 将返回POST\GET\DELETE等HTTP请求方式
	 * @return string
	 */
	public static function getRequestMethod() {
		return strtoupper(self::getServer('REQUEST_METHOD'));
	}
	
	
	/**
	 * 返回该请求是否为ajax请求
	 *
	 * 如果是ajax请求将返回true,否则返回false
	 * @return boolean
	 */
	public static function isAjax() {
		return !strcasecmp(self::getServer('HTTP_X_REQUESTED_WITH'), 'XMLHttpRequest');
	}
	
	/**
	 * 请求是否使用的是HTTPS安全链接
	 *
	 * 如果是安全请求则返回true否则返回false
	 * @return boolean
	 */
	public static function isSecure() {
		return !strcasecmp(self::getServer('HTTPS'), 'on');
	}
	
	/**
	 * 返回请求是否为GET请求类型
	 *
	 * 如果请求是GET方式请求则返回true，否则返回false
	 * @return boolean
	 */
	public static function isGet() {
		return !strcasecmp(self::getRequestMethod(), 'GET');
	}
	
	/**
	 * 返回请求是否为POST请求类型
	 *
	 * 如果请求是POST方式请求则返回true,否则返回false
	 *
	 * @return boolean
	 */
	public static function isPost() {
		return !strcasecmp(self::getRequestMethod(), 'POST');
	}
	
	/**
	 * 返回请求是否为PUT请求类型
	 *
	 * 如果请求是PUT方式请求则返回true,否则返回false
	 *
	 * @return boolean
	 */
	public static function isPut() {
		return !strcasecmp(self::getRequestMethod(), 'PUT');
	}
	
	/**
	 * 返回请求是否为DELETE请求类型
	 *
	 * 如果请求是DELETE方式请求则返回true,否则返回false
	 *
	 * @return boolean
	 */
	public static function isDelete() {
		return !strcasecmp(self::getRequestMethod(), 'Delete');
	}
	
	/**
	 * 初始化请求的资源标识符
	 *
	 * 这里的uri是去除协议名、主机名的
	 * <pre>Example:
	 * 请求： throws Exception/example/index.php?a=test
	 * 则返回: /example/index.php?a=test
	 * </pre>
	 *
	 * @return string
	 * @throws Exception 当获取失败的时候抛出异常
	 */
	public static function getRequestUri() {
		if (!self::$_requestUri) self::_initRequestUri();
		return self::$_requestUri;
	}
	
	/**
	 * 返回当前执行脚本的绝对路径
	 *
	 * <pre>Example:
	 * 请求: http://www.example.net/example/index.php?a=test
	 * 返回: /example/index.php
	 * </pre>
	 *
	 * @return string
	 * @throws Exception 当获取失败的时候抛出异常
	 */
	public static function getScriptUrl() {
		if (!self::$_scriptUrl) self::_initScriptUrl();
		return self::$_scriptUrl;
	}
	
	/**
	 * 返回执行脚本名称
	 *
	 * <pre>Example:
	 * 请求: http://www.example.net/example/index.php?a=test
	 * 返回: index.php
	 * </pre>
	 *
	 * @return string
	 * @throws Exception 当获取失败的时候抛出异常
	 */
	public static function getScript() {
		if (($pos = strrpos(self::getScriptUrl(), '/')) === false) $pos = -1;
		return substr(self::getScriptUrl(), $pos + 1);
	}
	
	/**
	 * 获取Http头信息
	 *
	 * @param string $header 头部名称
	 * @param string $default 获取失败将返回该值,默认为null
	 * @return string
	 */
	public static function getHeader($header, $default = null) {
		$temp = strtoupper(str_replace('-', '_', $header));
		if (substr($temp, 0, 5) != 'HTTP_') $temp = 'HTTP_' . $temp;
		if (($header = self::getServer($temp)) != null) return $header;
		if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
			if ($headers[$header]) return $headers[$header];
		}
		return $default;
	}
	
	/**
	 * 返回包含由客户端提供的、跟在真实脚本名称之后并且在查询语句（query string）之前的路径信息
	 *
	 * <pre>Example:
	 * 请求: http://www.example.net/example/index.php?a=test
	 * 返回: a=test
	 * </pre>
	 *
	 * @see $obj->getPathInfo()
	 * @return string
	 * @throws Exception
	 */
	public static function getPathInfo() {
		if (!self::$_pathInfo) self::_initPathInfo();
		return self::$_pathInfo;
	}
	
	/**
	 * 获取基础URL
	 *
	 * 这里是去除了脚本文件以及访问参数信息的URL地址信息:
	 *
	 * <pre>Example:
	 * 请求: http://www.example.net/example/index.php?a=test
	 * 1]如果: $absolute = false：
	 * 返回： example
	 * 2]如果: $absolute = true:
	 * 返回： http://www.example.net/example
	 * </pre>
	 * @param boolean $absolute 是否返回主机信息
	 * @return string
	 * @throws Exception 当返回信息失败的时候抛出异常
	 */
	public static function getBaseUrl($absolute = false) {
		if (self::$_baseUrl === null) self::$_baseUrl = rtrim(dirname(self::getScriptUrl()), '\\/.');
		return $absolute ? self::getHostInfo() . self::$_baseUrl : self::$_baseUrl;
	}
	
	/**
	 * 获得主机信息，包含协议信息，主机名，访问端口信息
	 *
	 * <pre>Example:
	 * 请求: http://www.example.net/example/index.php?a=test
	 * 返回： http://www.example.net/
	 * </pre>
	 * @see $obj->getHostInfo()
	 * @return string
	 * @throws Exception 获取主机信息失败的时候抛出异常
	 */
	public static function getHostInfo() {
		if (self::$_hostInfo === null) self::_initHostInfo();
		return self::$_hostInfo;
	}
	
	/**
	 * 返回当前运行脚本所在的服务器的主机名。
	 *
	 * 如果脚本运行于虚拟主机中
	 * 该名称是由那个虚拟主机所设置的值决定
	 * @return string
	 */
	public static function getServerName() {
		return self::getServer('SERVER_NAME', '');
	}
	
	/* (non-PHPdoc)
	 * @see $obj->getServerPort()
	*/
	public static function getServerPort() {
		if (!self::$_port) {
			$_default = self::isSecure() ? 443 : 80;
			self::setServerPort(self::getServer('SERVER_PORT', $_default));
		}
		return self::$_port;
	}
	
	/**
	 * 设置服务端口号
	 *
	 * https链接的默认端口号为443
	 * http链接的默认端口号为80
	 * @param int $port 设置的端口号
	 */
	public static function setServerPort($port) {
		self::$_port = (int) $port;
	}
	
	/**
	 * 返回浏览当前页面的用户的主机名
	 *
	 * DNS 反向解析不依赖于用户的 REMOTE_ADDR
	 *
	 * @return string
	 */
	public static function getRemoteHost() {
		return self::getServer('REMOTE_HOST');
	}
	
	/**
	 * 返回浏览器发送Referer请求头
	 *
	 * 可以让服务器了解和追踪发出本次请求的起源URL地址
	 *
	 * @return string
	 */
	public static function getUrlReferer() {
		return self::getServer('HTTP_REFERER');
	}
	
	/**
	 * 获得用户机器上连接到 Web 服务器所使用的端口号
	 *
	 * @return number
	 */
	public static function getRemotePort() {
		return self::getServer('REMOTE_PORT');
	}
	
	/**
	 * 返回User-Agent头字段用于指定浏览器或者其他客户端程序的类型和名字
	 *
	 * 如果客户机是一种无线手持终端，就返回一个WML文件；如果发现客户端是一种普通浏览器，
	 * 则返回通常的HTML文件
	 *
	 * @return string
	 */
	public static function getUserAgent() {
		return self::getServer('HTTP_USER_AGENT', '');
	}
	
	/**
	 * 返回当前请求头中 Accept: 项的内容，
	 *
	 * Accept头字段用于指出客户端程序能够处理的MIME类型，例如 text/html,image/*
	 *
	 * @return string
	 */
	public static function getAcceptTypes() {
		return self::getServer('HTTP_ACCEPT', '');
	}
	
	/**
	 * 返回客户端程序可以能够进行解码的数据编码方式
	 *
	 * 这里的编码方式通常指某种压缩方式
	 * @return string|''
	 */
	public static function getAcceptCharset() {
		return self::getServer('HTTP_ACCEPT_ENCODING', '');
	}
	
	/* (non-PHPdoc)
	 * @see $obj->getAcceptLanguage()
	*/
	public static function getAcceptLanguage() {
		if (!self::$_language) {
			$_language = explode(',', self::getServer('HTTP_ACCEPT_LANGUAGE', ''));
			self::$_language = $_language[0] ? $_language[0] : 'zh-cn';
		}
		return self::$_language;
	}
	
	
	
	/**
	 * 返回访问的IP地址
	 *
	 * <pre>Example:
	 * 返回：127.0.0.1
	 * </pre>
	 * @return string
	 */
	private static function _getClientIp() {
		if (($ip = self::getServer('HTTP_CLIENT_IP')) != null) {
			self::$_clientIp = $ip;
		} elseif (($_ip = self::getServer('HTTP_X_FORWARDED_FOR')) != null) {
			$ip = strtok($_ip, ',');
			do {
				$ip = ip2long($ip);
				if (!(($ip == 0) || ($ip == 0xFFFFFFFF) || ($ip == 0x7F000001) || (($ip >= 0x0A000000) && ($ip <= 0x0AFFFFFF)) || (($ip >= 0xC0A8FFFF) && ($ip <= 0xC0A80000)) || (($ip >= 0xAC1FFFFF) && ($ip <= 0xAC100000)))) {
					self::$_clientIp = long2ip($ip);
					return;
				}
			} while (($ip = strtok(',')));
		} elseif (($ip = self::getServer('HTTP_PROXY_USER')) != null) {
			self::$_clientIp = $ip;
		} elseif (($ip = self::getServer('REMOTE_ADDR')) != null) {
			self::$_clientIp = $ip;
		} else {
			self::$_clientIp = "0.0.0.0";
		}
	}
	
	/**
	 * 初始化请求的资源标识符
	 *
	 * <pre>这里的uri是去除协议名、主机名的
	 * Example:
	 * 请求： http://www.example.net/example/index.php?a=test
	 * 则返回: /example/index.php?a=test
	 * </pre>
	 * @throws Exception 处理错误抛出异常
	 */
	private static function _initRequestUri() {
		if (($requestUri = self::getServer('HTTP_X_REWRITE_URL')) != null) {
			self::$_requestUri = $requestUri;
		} elseif (($requestUri = self::getServer('REQUEST_URI')) != null) {
			self::$_requestUri = $requestUri;
			if (strpos(self::$_requestUri, self::getServer('HTTP_HOST')) !== false) 
				self::$_requestUri = preg_replace('/^\w+:\/\/[^\/]+/', '', self::$_requestUri);
		} elseif (($requestUri = self::getServer('ORIG_PATH_INFO')) != null) {
			self::$_requestUri = $requestUri;
			if (($query = self::getServer('QUERY_STRING')) != null) self::$_requestUri .= '?' . $query;
		} else
			throw new Exception(__CLASS__ . ' is unable to determine the request URI.');
	}
	
	/**
	 * 返回当前执行脚本的绝对路径
	 *
	 * <pre>Example:
	 * 请求: http://www.example.net/example/index.php?a=test
	 * 返回: /example/index.php
	 * </pre>
	 * @throws Exception 当获取失败的时候抛出异常
	 */
	private static function _initScriptUrl() {
		if (($scriptName = self::getServer('SCRIPT_FILENAME')) == null) {
			throw new Exception(__CLASS__ . ' determine the entry script URL failed!!!');
		}
		$scriptName = basename($scriptName);
		if (($_scriptName = self::getServer('SCRIPT_NAME')) != null && basename($_scriptName) === $scriptName) {
			self::$_scriptUrl = $_scriptName;
		} elseif (($_scriptName = self::getServer('PHP_SELF')) != null && basename($_scriptName) === $scriptName) {
			self::$_scriptUrl = $_scriptName;
		} elseif (($_scriptName = self::getServer('ORIG_SCRIPT_NAME')) != null && basename($_scriptName) === $scriptName) {
			self::$_scriptUrl = $_scriptName;
		} elseif (($pos = strpos(self::getServer('PHP_SELF'), '/' . $scriptName)) !== false) {
			self::$_scriptUrl = substr(self::getServer('SCRIPT_NAME'), 0, $pos) . '/' . $scriptName;
		} elseif (($_documentRoot = self::getServer('DOCUMENT_ROOT')) != null && ($_scriptName = self::getServer(
				'SCRIPT_FILENAME')) != null && strpos($_scriptName, $_documentRoot) === 0) {
				self::$_scriptUrl = str_replace('\\', '/', str_replace($_documentRoot, '', $_scriptName));
		} else
			throw new Exception(__CLASS__ . ' determine the entry script URL failed!!');
	}
	
	/**
	 * 获得主机信息，包含协议信息，主机名，访问端口信息
	 *
	 * <pre>Example:
	 * 请求: http://www.example.net/example/index.php?a=test
	 * 返回： http://www.example.net/
	 * </pre>
	 * @throws Exception 获取主机信息失败的时候抛出异常
	 */
	private static function _initHostInfo() {
		$http = self::isSecure() ? 'https' : 'http';
		if (($httpHost = self::getServer('HTTP_HOST')) != null)
			self::$_hostInfo = $http . '://' . $httpHost;
		elseif (($httpHost = self::getServer('SERVER_NAME')) != null) {
			self::$_hostInfo = $http . '://' . $httpHost;
			if (($port = self::getServerPort()) != null) self::$_hostInfo .= ':' . $port;
		} else
			throw new Exception(__CLASS__ . ' determine the entry script URL failed!!');
	}
	
	/**
	 * 返回包含由客户端提供的、跟在真实脚本名称之后并且在查询语句（query string）之前的路径信息
	 *
	 * <pre>Example:
	 * 请求: http://www.example.net/example/index.php?a=test
	 * 返回: a=test
	 * </pre>
	 * @throws Exception
	 */
	private static function _initPathInfo() {
		$requestUri = self::getRequestUri();
		$scriptUrl = self::getScriptUrl();
		$baseUrl = self::getBaseUrl();
		if (strpos($requestUri, $scriptUrl) === 0)
			$pathInfo = substr($requestUri, strlen($scriptUrl));
		elseif ($baseUrl === '' || strpos($requestUri, $baseUrl) === 0)
		$pathInfo = substr($requestUri, strlen($baseUrl));
		elseif (strpos($_SERVER['PHP_SELF'], $scriptUrl) === 0)
		$pathInfo = substr($_SERVER['PHP_SELF'], strlen($scriptUrl));
		else
			throw new Exception(__CLASS__ . ' determine the entry path info failed!!');
		if (($pos = strpos($pathInfo, '?')) !== false) $pathInfo = substr($pathInfo, $pos + 1);
		self::$_pathInfo = trim($pathInfo, '/');
	}
	
	/**
	 * 采用stripslashes反转义特殊字符
	 *
	 * @param array|string $data 待反转义的数据
	 * @return array|string 反转义之后的数据
	 */
	private static function _stripSlashes(&$data) {
		return is_array($data) ? array_map(array(self, '_stripSlashes'), $data) : stripslashes($data);
	}

}

