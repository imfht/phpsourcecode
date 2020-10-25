<?php
/**
 * Basic Security Filter Service
 * @author liuhui@2010-6-30 zzZero.L@2010-9-15
 * @status building
 * @from phpwind
 */
class iSecurity {
	/**
	 * html转换输出
	 * @param $param
	 * @return string
	 */
	public static function htmlEscape($param) {
		return trim(str_replace("\0", "&#0;", htmlspecialchars($param, ENT_QUOTES, 'utf-8')));
	}
	/**
	 * 初始化$_GET/$_POST为全局变量
	 * @param $keys
	 * @param $method
	 * @param $cvtype
	 */
	public static function globals($keys, $method = null, $cvtype = 1,$istrim = true) {
		!is_array($keys) && $keys = array($keys);
		foreach ($keys as $key) {
			if ($key == 'GLOBALS') continue;
			$GLOBALS[$key] = NULL;
			if ($method != 'P' && isset($_GET[$key])) {
				$GLOBALS[$key] = $_GET[$key];
			} elseif ($method != 'G' && isset($_POST[$key])) {
				$GLOBALS[$key] = $_POST[$key];
			}
			if (isset($GLOBALS[$key]) && !empty($cvtype) || $cvtype == 2) {
				$GLOBALS[$key] = self::escapeChar($GLOBALS[$key], $cvtype == 2, $istrim);
			}
		}
	}

	/**
	 * 指定key获取$_GET/$_POST变量
	 * @param $key
	 * @param $method
	 */
	public static function request($key, $method = null) {
		if ($method == 'G' || $method != 'P' && isset($_GET[$key])) {
			$value = $_GET[$key];
		}else{
			$value = $_POST[$key];
		}
		return self::escapeStr($value);
	}

	public static function get($key=null) {
		$value = self::escapeStr($key===null?$_GET:$_GET[$key]);
		$key===null && $_GET = $value;
		return $value;
	}
	public static function post($key=null) {
		$value = self::escapeStr($key===null?$_POST:$_POST[$key]);
		$key===null && $_POST = $value;
		return $value;
	}

	/**
	 * 全局变量过滤
	 */
	public static function filter() {
		$allowed = array('GLOBALS' => 1,'_GET' => 1,'_POST' => 1,'HTTP_RAW_POST_DATA' => 1,'_COOKIE' => 1,'_FILES' => 1,'_SERVER' => 1,'_APP' => 1);
		foreach ($GLOBALS as $key => $value) {
			if (!isset($allowed[$key])) {
				$GLOBALS[$key] = null;
				unset($GLOBALS[$key]);
			}
		}
		//兼容PHP5.3 magic_quotes_gpc=1
		define('GET_MAGIC_QUOTES_GPC', !@ini_get('magic_quotes_gpc'));

		if(GET_MAGIC_QUOTES_GPC){
			self::slashes($_POST);
			self::slashes($_GET);
			self::slashes($_COOKIE);
			self::slashes($_FILES);
		}

		self::get_server(array(
			'HTTP_REFERER','HTTP_HOST','HTTP_X_FORWARDED_FOR','HTTP_USER_AGENT',
			'HTTP_CLIENT_IP','HTTP_SCHEME','HTTPS','PHP_SELF','REMOTE_ADDR',
			'REQUEST_URI','REQUEST_METHOD','SCRIPT_NAME','REQUEST_TIME',
			'SERVER_SOFTWARE','SERVER_ADDR','SERVER_PORT',
			'X-Requested-With','HTTP_X_REQUESTED_WITH',
			'QUERY_STRING','argv','argc',
			'Authorization','HTTP_AUTHORIZATION'
		));
	}
	public static function filter_path($text) {
	    $text = str_replace('\\', '/', $text);
	    $text = str_replace(iPATH,iPHP_PROTOCOL,$text);
	    $pieces = explode('/', iPATH);
	    $count = count($pieces);
	    for ($i=0; $i < ceil($count/2); $i++) {
			$output = array_slice($pieces, 0, $count-$i);
			$path   = implode('/', $output);
	        if(stripos($text, $path)!==false){
	            $text = str_replace($path,iPHP_PROTOCOL,$text);
	        }
	    }
		return $text;
	}

	/**
	 * 通用多类型转换
	 * @param $mixed
	 * @param $isint
	 * @param $istrim
	 * @return mixture
	 */
	public static function escapeChar($mixed, $isint = false, $istrim = false) {
		if (is_array($mixed)) {
			foreach ($mixed as $key => $value) {
				$mixed[$key] = self::escapeChar($value, $isint, $istrim);
			}
		} elseif ($isint) {
			$mixed = (int) $mixed;
		} elseif (!is_numeric($mixed) && ($istrim ? $mixed = trim($mixed) : $mixed) && $mixed) {
			$mixed = self::escapeStr($mixed);
		}
		return $mixed;
	}
	/**
	 * 字符转换
	 * @param $data
	 * @return string
	 */
	public static function escapeStr($data) {
		if (is_array($data)) {
			$data = array_map(array(__CLASS__,'escapeStr'), $data);
		}else{
	        $data = str_replace(array("\0","%00","\r"),'',$data);
			$data = preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','', $data);
			//& => &amp;
			$data = preg_replace('/&(?!(#[0-9]+|[a-z]+);)/is', '&amp;', $data);
			//&amp;#xA9 => &#xA9;
			$data = preg_replace('/&amp;#x([a-fA-F0-9]{2,4});/', '&#x\\1',$data);
			$data = str_replace(array('\"',"\'","\\\\"), array('&#34;','&#39;','&#92;'), $data);
	        $data = str_replace(array("%3C", '<'), '&#60;', $data);
	        $data = str_replace(array("%3E", '>'), '&#62;', $data);
			$data = str_replace(array('"',"'"), array('&#34;','&#39;'), $data);
	    }
	    return $data;
	}
	public static function html_decode($string) {
		if (is_array($string)) {
			$string = array_map(array(__CLASS__,'html_decode'), $string);
		}else{
			$string = htmlspecialchars_decode($string);
			$string = str_replace(
				array('&#92;','&#60;','&#62;','&#39;','&#34;'),
				array('\\','<','>',"'",'"'),
			$string);
		}
		return $string;
	}

	/**
	 * 变量转义
	 * @param $array
	 */
	public static function _addslashes(&$data) {
		return self::slashes($data);
	}
	public static function slashes(&$data) {
		if (is_object($data)) {
			foreach ($data as $key => &$value) {
				self::slashes($value);
			}
		}elseif (is_array($data)) {
			$data = array_map(array(__CLASS__,'slashes'), $data);
		}else{
			$data = addslashes($data);
		}
		return $data;
	}

	/**
	 * 获取服务器变量
	 * @param $keys
	 * @return string
	 */
	public static function get_server($keys) {
		// Fix for IIS when running with PHP ISAPI
		if ( empty($_SERVER['REQUEST_URI'] ) || ( php_sapi_name() != 'cgi-fcgi' && preg_match( '/^Microsoft-IIS\//',$_SERVER['SERVER_SOFTWARE'] ) ) ) {
		    if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) {
		       $_SERVER['REQUEST_URI'] =$_SERVER['HTTP_X_ORIGINAL_URL'];
		    }else if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
		       $_SERVER['REQUEST_URI'] =$_SERVER['HTTP_X_REWRITE_URL'];
		    }else {
		        // Use ORIG_PATH_INFO if there is no PATH_INFO
		        if ( !isset($_SERVER['PATH_INFO']) && isset($_SERVER['ORIG_PATH_INFO']) )
		           $_SERVER['PATH_INFO'] =$_SERVER['ORIG_PATH_INFO'];

		        // Some IIS + PHP configurations puts the script-name in the path-info (No need to append it twice)
		        if ( isset($_SERVER['PATH_INFO']) ) {
		            if ($_SERVER['PATH_INFO'] ==$_SERVER['SCRIPT_NAME'] )
		               $_SERVER['REQUEST_URI'] =$_SERVER['PATH_INFO'];
		            else
		               $_SERVER['REQUEST_URI'] =$_SERVER['SCRIPT_NAME'] .$_SERVER['PATH_INFO'];
		        }

		        // Append the query string if it exists and isn't null
		        if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
		           $_SERVER['REQUEST_URI'] .= '?' .$_SERVER['QUERY_STRING'];
		        }
		    }
		}

		// Fix for PHP as CGI hosts that set SCRIPT_FILENAME to something ending in php.cgi for all requests
		if ( isset($_SERVER['SCRIPT_FILENAME']) && ( strpos($_SERVER['SCRIPT_FILENAME'], 'php.cgi') == strlen($_SERVER['SCRIPT_FILENAME']) - 7 ) )
		   $_SERVER['SCRIPT_FILENAME'] =$_SERVER['PATH_TRANSLATED'];

		// Fix for ther PHP as CGI hosts
		if (strpos($_SERVER['SCRIPT_NAME'], 'php.cgi') !== false)
		    unset($_SERVER['PATH_INFO']);

		if ( empty($_SERVER['PHP_SELF']) )
		   $_SERVER['PHP_SELF'] = preg_replace("/(\?.*)?$/",'',$_SERVER["REQUEST_URI"]);


		foreach ($_SERVER as $key=>$sval){
			if (in_array($key, $keys)) {
				$sval = str_replace(array('<','>','"',"'",'%3C','%3E','%22','%27','%3c','%3e'), '',$sval);
				$_SERVER[$key] = str_replace(array("\0","\x0B", "%00", "\r"), '', $sval);
			}else{
				unset($_SERVER[$key]);
			}
		}
		self::slashes($_SERVER);
	}
    public static function encoding($string,$code='UTF-8') {
        $encode = mb_detect_encoding($string, array("ASCII","UTF-8","GB2312","GBK","BIG5"));
        if(strtoupper($encode)!=$code){
            if (function_exists('mb_convert_encoding')) {
                $string = mb_convert_encoding($string,$code,$encode);
            } elseif (function_exists('iconv')) {
                $string = iconv($encode,$code, $string);
            }
        }
        return $string;
	}
	public static function safeStr($string) {
		return is_array($string) ?
			array_map("iSecurity::safeStr", $string) :
			preg_replace('/\W+/is','',$string);
	}
    public static function secureToken($token){
        for ($i=0; $i <100 ; $i++) {
            $token = sha1(md5($token));
        }
        return $token;
    }
}
