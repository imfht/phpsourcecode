<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// ------------------------------------------------------------------------

if ( ! function_exists('load_config'))
{
	/**
	 * 加载配置文件 支持格式转换 仅支持一级配置
	 * @param string $file 配置文件名
	 * @param string $parse 配置解析方法 有些格式需要用户自己解析
	 * @return array
	 */
	function load_config($file){
	    $ext  = pathinfo($file,PATHINFO_EXTENSION);
	    switch($ext){
	        case 'php':
	            return include $file;
	        case 'ini':
	            return parse_ini_file($file);
	        case 'yaml':
	            return yaml_parse_file($file);
	        case 'xml': 
	            return (array)simplexml_load_file($file);
	        case 'json':
	            return json_decode(file_get_contents($file), true);
	        default:
	        	throw new Exception("not support file ext", 0);
	    }
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('xml_encode'))
{
	/**
	 * XML编码
	 * @param mixed $data 数据
	 * @param string $root 根节点名
	 * @param string $item 数字索引的子节点名
	 * @param string $attr 根节点属性
	 * @param string $id   数字索引子节点key转换的属性名
	 * @param string $encoding 数据编码
	 * @return string
	 */
	function xml_encode($data, $root='think', $item='item', $attr='', $id='id', $encoding='utf-8') {
	    if(is_array($attr)){
	        $_attr = array();
	        foreach ($attr as $key => $value) {
	            $_attr[] = "{$key}=\"{$value}\"";
	        }
	        $attr = implode(' ', $_attr);
	    }
	    $attr   = trim($attr);
	    $attr   = empty($attr) ? '' : " {$attr}";
	    $xml    = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
	    $xml   .= "<{$root}{$attr}>";
	    $xml   .= data_to_xml($data, $item, $id);
	    $xml   .= "</{$root}>";
	    return $xml;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('data_to_xml'))
{
	/**
	 * 数据XML编码
	 * @param mixed  $data 数据
	 * @param string $item 数字索引时的节点名称
	 * @param string $id   数字索引key转换为的属性名
	 * @return string
	 */
	function data_to_xml($data, $item='item', $id='id') {
	    $xml = $attr = '';
	    foreach ($data as $key => $val) {
	        if(is_numeric($key)){
	            $id && $attr = " {$id}=\"{$key}\"";
	            $key  = $item;
	        }
	        $xml    .=  "<{$key}{$attr}>";
	        $xml    .=  (is_array($val) || is_object($val)) ? data_to_xml($val, $item, $id) : $val;
	        $xml    .=  "</{$key}>";
	    }
	    return $xml;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('dump'))
{
	/**
	 * 浏览器友好的变量输出
	 * @param mixed $var 变量
	 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
	 * @param string $label 标签 默认为空
	 * @param boolean $strict 是否严谨 默认为true
	 * @return void|string
	 */
	function dump($var, $echo=true, $label=null, $strict=true) {
	    $label = ($label === null) ? '' : rtrim($label) . ' ';
	    if (!$strict) {
	        if (ini_get('html_errors')) {
	            $output = print_r($var, true);
	            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
	        } else {
	            $output = $label . print_r($var, true);
	        }
	    } else {
	        ob_start();
	        var_dump($var);
	        $output = ob_get_clean();
	        if (!extension_loaded('xdebug')) {
	            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
	            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
	        }
	    }
	    if ($echo) {
	        echo($output);
	        return null;
	    }else
	        return $output;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('is_ssl'))
{
	/**
	 * 判断是否SSL协议
	 * @return boolean
	 */
	function is_ssl() {
	    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
	        return true;
	    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
	        return true;
	    }
	    return false;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('to_guid_string'))
{
	/**
	 * 根据PHP各种类型变量生成唯一标识号
	 * @param mixed $mix 变量
	 * @return string
	 */
	function to_guid_string($mix) {
	    if (is_object($mix)) {
	        return spl_object_hash($mix);
	    } elseif (is_resource($mix)) {
	        $mix = get_resource_type($mix) . strval($mix);
	    } else {
	        $mix = serialize($mix);
	    }
	    return md5($mix);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('in_array_case'))
{
	/**
	 * 不区分大小写的in_array实现
	 */ 
	function in_array_case($value,$array){
	    return in_array(strtolower($value),array_map('strtolower',$array));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('send_http_status'))
{
	/**
	 * 发送HTTP状态
	 * @param integer $code 状态码
	 * @return void
	 */
	function send_http_status($code) {
	    static $_status = array(
	            // Informational 1xx
	            100 => 'Continue',
	            101 => 'Switching Protocols',
	            // Success 2xx
	            200 => 'OK',
	            201 => 'Created',
	            202 => 'Accepted',
	            203 => 'Non-Authoritative Information',
	            204 => 'No Content',
	            205 => 'Reset Content',
	            206 => 'Partial Content',
	            // Redirection 3xx
	            300 => 'Multiple Choices',
	            301 => 'Moved Permanently',
	            302 => 'Moved Temporarily ',  // 1.1
	            303 => 'See Other',
	            304 => 'Not Modified',
	            305 => 'Use Proxy',
	            // 306 is deprecated but reserved
	            307 => 'Temporary Redirect',
	            // Client Error 4xx
	            400 => 'Bad Request',
	            401 => 'Unauthorized',
	            402 => 'Payment Required',
	            403 => 'Forbidden',
	            404 => 'Not Found',
	            405 => 'Method Not Allowed',
	            406 => 'Not Acceptable',
	            407 => 'Proxy Authentication Required',
	            408 => 'Request Timeout',
	            409 => 'Conflict',
	            410 => 'Gone',
	            411 => 'Length Required',
	            412 => 'Precondition Failed',
	            413 => 'Request Entity Too Large',
	            414 => 'Request-URI Too Long',
	            415 => 'Unsupported Media Type',
	            416 => 'Requested Range Not Satisfiable',
	            417 => 'Expectation Failed',
	            // Server Error 5xx
	            500 => 'Internal Server Error',
	            501 => 'Not Implemented',
	            502 => 'Bad Gateway',
	            503 => 'Service Unavailable',
	            504 => 'Gateway Timeout',
	            505 => 'HTTP Version Not Supported',
	            509 => 'Bandwidth Limit Exceeded'
	    );
	    if(isset($_status[$code])) {
	        header('HTTP/1.1 '.$code.' '.$_status[$code]);
	        // 确保FastCGI模式下正常
	        header('Status:'.$code.' '.$_status[$code]);
	    }
	}
}

// ------------------------------------------------------------------------