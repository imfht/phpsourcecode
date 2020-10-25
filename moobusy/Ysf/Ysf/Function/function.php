<?php
/**
 * autoload
 */
function ysf_auto_load($class){
	$path = explode('\\', $class);
	if (count($path)==3 && $path[0]=='App') {
		$file = APP_PATH . '/'. strtolower($path[1]) . '/' . strtolower($path[2]) . '.php';
		if (file_exists($file)) {
			include_once $file;
		}
	}
}
/**
 * config set and get
 * @return *	  键值
 */
function config()
{
	$args = func_get_args();
	if (empty($args)) {
		return \Ysf\Config::get();
	}elseif (!isset($args[1])) {
		return \Ysf\Config::get($args[0]);
	}else{
		return \Ysf\Config::set($args[0],$args[1]);
	}
}
/**
 * cache set and get
 * @return [type] [description]
 */
function cache()
{
	$args = func_get_args();
	if (empty($args)) {
		return \Ysf\Cache::get();
	}elseif (!isset($args[1])) {
		return \Ysf\Cache::get($args[0]);
	}else{
		return \Ysf\Cache::set($args[0],$args[1]);
	}
}

/**
 * db 
 */
function db($db='')
{
	static $dbs;
	$db = $db ?: 'default';

	if (!is_object($dbs)) {
		$dbs = new \Ysf\Model;
	}

	if (!isset($dbs->conn[$db])) {
		$dbs->init($db);
	}

	return $dbs->choose($db);
}
/**
 * request 
 */
function request($name='',$default=''){
	$explode = explode('.', trim($name));
	if (count($explode)>1) {
		$type = strtolower($explode[0]);
		$name = @$explode[1];
	}else{
		$type = '';
		$name = trim($name);
	}

	$cookie_name = config('cookie/prev').$name;
	$header_name = 'HTTP_'.strtoupper(str_replace('-', '_', $name));
	switch ($type) {
		case 'get':
			return isset($_GET[$name]) ? $_GET[$name] : $default;
			break;
		case 'post':
			return isset($_POST[$name]) ? $_POST[$name] : $default;
			break;
		case 'cookie':
			return isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : $default;
			break;
		case 'header':
			return isset($_SERVER[$header_name]) ? $_SERVER[$header_name] : $default;
			break;
		default:
			if (isset($_GET[$name])) {
				return $_GET[$name];
			}elseif(isset($_POST[$name])){
				return $_POST[$name];
			}elseif(isset($_COOKIE[$cookie_name])){
				return $_COOKIE[$cookie_name];
			}elseif(isset($_SERVER[$header_name])){
				return $_SERVER[$header_name];
			}else{
				return $default;
			}
			break;
	}

}

/**
 * cookie set and get
 */
function cookie($name, $val='', $time='86400'){
	if (count(func_get_args())>1) {
		return setcookie(
			config('cookie/prev').$name,
			$val,
			TIME+$time,
			config('cookie/path'),
			config('cookie/domain'));
	}else{
		return request('cookie.'.$name);
	}
}

/**
 * authcode    encode and decode
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {  
	// 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙  
	$ckey_length = 4;  
	   
	// 密匙  
	$key = md5($key ? $key : config('authcode'));  
	   
	// 密匙a会参与加解密  
	$keya = md5(substr($key, 0, 16));  
	// 密匙b会用来做数据完整性验证  
	$keyb = md5(substr($key, 16, 16));  
	// 密匙c用于变化生成的密文  
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';  
	// 参与运算的密匙  
	$cryptkey = $keya.md5($keya.$keyc);  
	$key_length = strlen($cryptkey);  
	// 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性  
	// 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确  
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;  
	$string_length = strlen($string);  
	$result = '';  
	$box = range(0, 255);  
	$rndkey = [];  
	// 产生密匙簿  
	for($i = 0; $i <= 255; $i++) {  
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);  
	}  
	// 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度  
	for($j = $i = 0; $i < 256; $i++) {  
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;  
		$tmp = $box[$i];  
		$box[$i] = $box[$j];  
		$box[$j] = $tmp;  
	}  
	// 核心加解密部分  
	for($a = $j = $i = 0; $i < $string_length; $i++) {  
		$a = ($a + 1) % 256;  
		$j = ($j + $box[$a]) % 256;  
		$tmp = $box[$a];  
		$box[$a] = $box[$j];  
		$box[$j] = $tmp;  
		// 从密匙簿得出密匙进行异或，再转成字符  
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));  
	}  
	if($operation == 'DECODE') {  
		// substr($result, 0, 10) == 0 验证数据有效性  
		// substr($result, 0, 10) - time() > 0 验证数据有效性  
		// substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性  
		// 验证数据有效性，请看未加密明文的格式  
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {  
			return substr($result, 26);  
		} else {  
			return '';  
		}  
	} else {  
		// 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因  
		// 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码  
		return $keyc.str_replace('=', '', base64_encode($result));  
	}  
}

/**
 * pagination
 * $count 
 * $limit 
 * $page 
 * $maxshow 
 */
function pagination($count,$limit,$page,$maxshow=20,$url='')
{
	$result = [];

	if (empty($url)) {
		$url = '//'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	$_url = parse_url($url);
	if (isset($_url['query'])) {
		$_url['query'] = explode('&', $_url['query']);
		foreach ($_url['query'] as $k => $v) {
			if (strtolower(explode('=', $v)[0])=='p' || strtolower(explode('=', $v)[0])=='pnum' ) {
				unset($_url['query'][$k]);
			}
		}
	}else{
		$_url['query'] = [];
	}

	$url = '//' . $_url['host'] . (isset($_url['path']) ? $_url['path'] : '/') . '?' ;
	
	$pagecount = $count>0 ? (int)ceil($count/$limit) : 1;
	$page = $page>$pagecount ? $pagecount : $page;
	for ($i=1; $i <= $pagecount ; $i++) { 
		$result[$i] = $url . implode('&', array_merge_recursive($_url['query'],array("p={$i}","pnum={$limit}")));
	}

	if ($page+intval($maxshow/2)>=count($result)) {
		$result = array_slice($result, 0-$maxshow,$maxshow,true);
	}elseif($page<=ceil($maxshow/2)){
		$result = array_slice($result, 0,$maxshow,true);
	}else{
		$result = array_slice($result, $page-ceil($maxshow/2),$maxshow,true);
	}

	return $result;
}