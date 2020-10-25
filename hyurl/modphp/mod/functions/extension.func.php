<?php
/** PHP 扩展函数 */
/**
 * camelcase2underline() 将使用驼峰法命名的字符串转为下划线命名
 * @param  string $str 驼峰命名字符串
 * @return string      下划线命名字符串
 */
function camelcase2underline($str){
	return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $str));
}

/**
 * underline2camelcase() 将使用下划线命名的字符串转换为驼峰法命名
 * @param  string   $str     下划线命名字符串
 * @param  boolean  $ucfirst [可选]首字母大写，默认 false
 * @return string            驼峰命名字符串
 */
function underline2camelcase($str, $ucfirst = false){
	$str = preg_replace_callback('/_([a-zA-Z0-9])/', function($match){
		return strtoupper(ltrim($match[0], '_'));
	}, $str);
	return $ucfirst ? ucfirst($str) : $str;
}

/** 
 * str2bin() 将字符串转换为二进制数字
 * @param  string $str 字符串
 * @return string      二进制数字
 */
function str2bin($str){
	$arr = preg_split('/(?<!^)(?!$)/u', $str);
	foreach($arr as &$v){
		$v = unpack('H*', $v);
		$v = base_convert($v[1], 16, 2);
	}
	return join(' ', $arr);
}

/**
 * bin2str() 将二进制数字转换为字符串
 * @param  string $str 二进制数字
 * @return string      字符串
 */
function bin2str($str){
	$arr = explode(' ', $str);
	foreach($arr as &$v){
		$v = base_convert($v, 2, 16);
		if(strlen($v) % 2) $v = '0'.$v; //补齐长度
		$v = pack('H*', $v);
	}
	return join('', $arr);
}

/**
 * md5_crypt() 生成一个随机的 MD5 哈希秘钥
 * @param  string $str 字符串
 * @return string      哈希值
 */
function md5_crypt($str){
	return crypt($str, '$1$'.rand_str(8).'$');
}

if(!function_exists('password_verify')):
/**
 * password_verify() 验证一个密码是否与哈希密钥相等
 * @param  string $password 原始密码
 * @param  string $hash     哈希密钥
 * @return boolean          相等返回 true，否则返回 false
 */
function password_verify($password, $hash){
	return $hash == crypt($password, $hash);
}
endif;

/**
 * get_uploaded_files() 获取上传文件的数组，与 $_FILES 不同，当同一个键名包含多个文件时，
 *                      这个键的值是一个索引数组，数组下面是一个包含文件信息的关联数组
 * @param  string $key  [可选]设置只获取指定键(一维键名)的文件信息，如不设置，则返回所有上传文件的信息
 * @return array        包含所有上传文件的数组, 如果没有上传的文件，或者设置获取的键没有文件，则返回空数组
 */
function get_uploaded_files($key = ''){
	$files = $_FILES;
	foreach ($files as &$file){
		if(is_array($file)){
			$_files = array();
			foreach ($file as $prop => $val){
				if(is_array($val)){
					array_walk_recursive($val, function(&$item) use ($prop){
						$item = array($prop => $item); //$item 是遍历 $val 时产生的值
					}, $file);
					$_files = array_replace_recursive($_files, $val);
				}else{
					$_files[$prop] = $val;
				}
			}
			$file = $_files;
		}
	}
	return $key ? (isset($files[$key]) ? $files[$key] : false) : $files;
}

/**
 * array_xmerge() 递归、深层增量地合并数组
 * @param  array $array 待合并的数组
 * @return array        合并后的数组
 */
function array_xmerge(array $array){
	switch(func_num_args()){
		case 1: return $array; break;
		case 2:
			$args = func_get_args();
			$args[2] = array();
			if(is_array($args[0]) && is_array($args[1])){
				foreach(array_unique(array_merge(array_keys($args[0]),array_keys($args[1]))) as $k){
					if(isset($args[0][$k]) && isset($args[1][$k]) && is_array($args[0][$k]) && is_array($args[1][$k]))
						$args[2][$k] = array_xmerge($args[0][$k], $args[1][$k]);
					elseif(isset($args[0][$k]) && isset($args[1][$k]))
						$args[2][$k] = $args[1][$k];
					elseif(isset($args[0][$k]) || !isset($args[1][$k]))
						$args[2][$k] = $args[0][$k];
					elseif(!isset($args[0][$k]) || isset($args[1][$k]))
						$args[2][$k] = $args[1][$k];
				}
				return $args[2];
			}else{
				return $args[1]; break;
			}
		default:
			$args = func_get_args();
			$args[1] = array_xmerge($args[0], $args[1]);
			array_shift($args);
			return call_user_func_array('array_xmerge', $args); //递归并将 $args 作为多个参数转入
			break;
	}
}

/**
 * xscandir() 递归扫描目录结构
 * @param  string   $dir     起始目录
 * @param  integer  $sort    [可选]排序，0 升序(默认)，1 降序
 * @return array             目录树，如果提供的 $dir 不是一个目录名，则返回 false
 */
function xscandir($dir, $sort = 0){
	if(!is_dir($dir)) return false;
	$tree = array();
	foreach(scandir($dir, $sort) as $file){
		if(is_dir("$dir/$file") && $file != '.' && $file != '..'){
			$tree[$file] = xscandir("$dir/$file"); //扫描子目录
		}else{
			$tree[] = $file;
		}
	}
	return $tree;
}

/**
 * xrmdir() 强制删除目录，无论目录是否为空
 * @param  string $dir 目录名称
 * @return bool
 */
function xrmdir($dir){
	if(!is_dir($dir)) return false;
	$files = array_diff(scandir($dir), array('.', '..'));
	foreach($files as $file){
		//删除目录下的文件，如果是文件夹，则递归地删除
		$bool = is_dir("$dir/$file") ? xrmdir("$dir/$file") : unlink("$dir/$file");
		if(!$bool) return false;
	}
	return rmdir($dir);
}

/**
 * xcopy() 复制目录和它的文件
 * @param  string $src   源地址
 * @param  string $dst   目标地址
 * @param  bool   $cover [可选] true 覆盖已存在的文件；false(默认)，跳过已存在的文件
 * @return bool
 */
function xcopy($src, $dst, $cover = false){
	if(!file_exists($src) || (is_file($dst) && !$cover)) return false;
	if(is_dir($src)){
		@mkdir($dst);
		$files = array_diff(scandir($src), array('.', '..'));
		foreach($files as $file){
			xcopy("$src/$file", "$dst/$file", $cover); //递归复制文件(夹)
		}
		return true;
	}
	return copy($src, $dst);
}

/**
 * xchmod() 尝试更改文件（夹）属性并应用到子文件（夹）
 * @param  string   $path 文件（夹）路径
 * @param  int(oct) $mode 属性模式
 * @return bool
 */
function xchmod($path, $mode){
	$ok = false;
	if(is_dir($path)){
		foreach(array2path(xscandir($path)) as $file){ //遍历目录下的所有文件
			if(strrpos($file, '..') != strlen($file)-2){
				$ok = chmod($path.'/'.$file, $mode);
			}
		}
	}elseif(file_exists($path)){
		$ok = chmod($path, $mode);
	}
	return $ok;
}

/**
 * array2path() 将数组结构化数据转换为路径
 * @param  array  $array 数组结构数据
 * @param  string $dir   [可选]设置父目录
 * @return array         结构数据路径
 */
function array2path(array $array, $dir = ""){
	$paths = array();
	if($dir && $dir[strlen($dir)-1] != '/') $dir .= '/';
	foreach ($array as $k => $v){
		if(is_array($v)){
			$paths = array_merge($paths, array2path($v, $dir.$k));
		}else{
			$paths[] = str_replace(array('/', '\\\\'), DIRECTORY_SEPARATOR, $dir.$v); //使用系统的目录分隔符
		}
	}
	return $paths;
}

if(extension_loaded('zip')):
/**
 * zip_compress() 快速压缩文件（夹）为 ZIP
 * @param  string $path 文件（夹）路径
 * @param  string $file ZIP 文件名
 * @return bool
 */
function zip_compress($path, $file){
	$zip = new ZipArchive();
	$zip->open($file, ZipArchive::CREATE | ZipArchive::OVERWRITE); //以新建/重写模式打开 ZIP
	$ok = false;
	if(is_dir($path)){ //压缩文件夹
		foreach(array2path(xscandir($path)) as $file){
			$_file = $_file = $path.'/'.$file; //本地文件名
			if(is_file($_file)){
				$ok = $zip->addFile($_file, $file); //将文件添加到 ZIP 中
			}elseif(is_empty_dir($_file)){
				$ok = $zip->addEmptyDir(rtrim($file, '/\\.')); //将空文件夹添加到 ZIP 中
			}
		}
	}elseif(file_exists($path)){ //仅压缩文件
		$i = strrpos($path, '/');
		if($i !== false) $i++;
		$ok = $zip->addFile($path, substr($path, $i));
	}
	$zip->close();
	return $ok;
}

/**
 * zip_extract() 解压 ZIP 到指定目录
 * @param  string $file ZIP 文件名
 * @param  string $path 解压路径
 * @return bool
 */
function zip_extract($file, $path){
	$zip = new ZipArchive();
	$ok = $zip->open($file) ? @$zip->extractTo($path) : false;
	$zip->close();
	return $ok;
}

/**
 * zip_list() 列出一个 ZIP 压缩文件夹中的所有文件
 * @param  string $filename ZIP 文件名
 * @param  bool   $noFolder [可选]不包含文件夹，默认 false
 * @return array            包含所有文件名的数组
 */
function zip_list($filename, $noFolder = false){
	$list = array();
	$zip = zip_open($filename);
	while ($zip && $entry = zip_read($zip)) {
		$name = zip_entry_name($entry);
		if(!$noFolder || $name[strlen($name)-1] != '/'){
			$list[] = $name;
		}
	}
	return $list;
}
endif;

/**
 * rand_str() 获取随机字符串
 * @param  integer $len   [可选]字符串长度，默认 4
 * @param  string  $chars [可选]可能出现的字符序列
 * @return string         随机的字符串
 */
function rand_str($len = 4, $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'){
	for($i=0, $str="", $end=strlen($chars)-1; $i<$len; $i++){
		$str .= $chars[rand(0, $end)];
	}
	return $str;
}

/**
 * escape_tags() 转义字符串中的 HTML 标签
 * @param  string $str  待转义的字符串
 * @param  string $tags 需转义的标签
 * @return string       转义后的字符串
 */
function escape_tags($str, $tags){
	$tags = explode('><', str_replace(' ', '', $tags));
	foreach ($tags as $tag){
		$tag = trim($tag, '< >');
		$re1 = array( //The original tags (start and end)
			'/<'.$tag.'([\s\S]*)>([\s\S]*)<\/'.$tag.'[\s\S]*>/Ui', //start tag
			'/<'.$tag.'([\s\S]*)>/Ui' //end tag
			);
		$re2 = array( //Replacements, replace < and > to &lt; and &gt;
			'&lt;'.$tag.'$1&gt;$2&lt;/'.$tag.'&gt;',
			'&lt;'.$tag.'$1&gt;'
			);
		$str = preg_replace($re1, $re2, $str);
	}
	return $str;
}

/**
 * export() 输出变量的原始信息
 * @param  mixed  $var  变量名
 * @param  string $path [可选]输出到文件
 * @return mixed        如果输出到文件，则返回写出字符长度，否则返回 null
 */
function export($var, $path = ''){
	$str = var_export($var, true);
	if($path){
		return file_put_contents($path, "<?php\nreturn ".$str.';'); //将代码输出到文件
	}elseif(is_browser() && !is_ajax()){
		$str = trim(highlight_string("<?php\n".$str, true)); //高亮代码
		if($var === null || is_int($var) || is_bool($var) || is_object($var) || is_resource($var)){
			$_str = '&lt;?php<br />'; //for special variables
		}else{
			$_str = '<span style="color: #0000BB">&lt;?php<br /></span>'; //for normal variables
		}
		echo strstr($str, $_str, true).substr($str, strpos($str, $_str)+strlen($_str))."<br/>";
	}else{
		echo $str."\n";
	}
}

/**
 * function_alias() 创建函数别名
 * @param  string  $original 原函数名
 * @param  string  $alias    函数别名
 * @return boolean           如果创建别名成功则返回 true, 否则返回 false
 */
function function_alias($original, $alias){
	if(!function_exists($original) || function_exists($alias)) return false;
	$i = strrpos($alias, '\\');
	$ns = substr($alias, 0, $i); //命名空间
	$alias = substr($alias, $i !== false ? $i+1 : 0);
	$original = var_export($original, true);
	$code = "namespace $ns{function $alias(){return call_user_func_array($original, func_get_args());}}"; //运行代码创建函数
	eval($code);
	return true;
}

if(extension_loaded('mbstring')):
/**
 * unicode_encode() 将字符串进行 unicode 编码
 * @param  string $str 字符串
 * @return string      编码后的字符串
 */
function unicode_encode($str){
	$str = unpack('H*', mb_convert_encoding($str, 'UCS-2BE', 'UTF-8'));
	return '\u'.implode('\u', str_split($str[1], 4));
}

/**
 * unicode_decode() 解码 unicode 字符串
 * @param  string $str 加密的 unicode 字符串
 * @return string 	   解码后的字符串
 */
function unicode_decode($str){
	return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function($matches){
		return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");
	}, $str);
}

/**
 * mb_str_split() 将字符串分割为数组
 * @param  string $str     待分割的字符串
 * @param  int    $len     [可选]每个数组元素的长度
 * @param  string $charset [可选]编码
 * @return array           分割成的数组
 */
function mb_str_split($str, $len = 1, $charset = 'UTF-8'){
	$start = 0;
	$strlen = mb_strlen($str);
	while ($strlen){
		$array[] = mb_substr($str, $start, $len, $charset);
		$str = mb_substr($str, $len, $strlen, $charset);
		$strlen = mb_strlen($str);
	}
	return $array;
}

/**
 * is_ascii() 判断一个字符串是否只包含 ASCII 字符
 * @param  string  $str 待检测的字符串
 * @return boolean
 */
function is_ascii($str){
	return strlen($str) === mb_strlen($str, "UTF-8");
}
endif;

/**
 * is_assoc() 判断一个变量是否为完全关联数组
 * @param  array   $input 待判断的变量
 * @return boolean
 */
function is_assoc($input){
	if(!is_array($input) || !$input) return false;
	return array_keys($input) !== range(0, count($input) - 1);
}

/**
 * implode_assoc() 将关联数组合并为字符串
 * @param  array  $assoc 待合并的关联数组
 * @param  string $sep   分割符
 * @param  string $sep2  第二分割符
 * @return string        合并后的字符串
 */
function implode_assoc($assoc, $sep, $sep2){
	$arr = array();
	foreach ($assoc as $key => $value){
		$arr[] = $key.$sep.$value;
	}
	return implode($sep2, $arr);
}

/**
 * explode_assoc() 将字符串分割为关联数组
 * @param  string $str  待分割的字符串
 * @param  string $sep  分割符
 * @param  string $sep2 第二分割符
 * @return array        分割后的关联数组
 */
function explode_assoc($str, $sep, $sep2){
	if(!$str) return array();
	$arr = explode($sep, $str);
	$assoc = array();
	foreach ($arr as $value){
		$i = strpos($value, $sep2);
		$k = substr($value, 0, $i);
		$v = substr($value, $i+1) ?: '';
		$assoc[$k] = $v;
	}
	return $assoc;
}

/**
 * is_empty_dir() 判断目录是否为空
 * @param  string  $dir 名录名
 * @return boolean
 */
function is_empty_dir($dir){
	return is_dir($dir) && count(scandir($dir)) <= 2;
}

/**
 * is_img() 判断文件是否为图片
 * @param  string  $src     文件源地址
 * @param  boolean $strict  [可选]严格模式，默认 false
 * @return boolean
 */
function is_img($src, $strict = false){
	if(!$strict || !function_exists('mime_content_type')){
		$ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
		return in_array($ext, array('jpg','jpeg','png','gif','bmp')); //compare extension name
	}
	return strpos(mime_content_type($src), 'image/') === 0; //比较 MimeType
}
function_alias('is_img', 'is_image');

/**
 * is_agent() 判断当前是否为客户端请求
 * @param  mixed    $agent  [可选]客户端类型，或者设置为 true 判断是否有 User-Agent 请求头
 * @return boolean
 */
function is_agent($agent = ''){
	if(PHP_SAPI == 'cli') return false;
	$hasAgent = !empty($_SERVER['HTTP_USER_AGENT']);
	if($agent === true || $agent === 1) return $hasAgent;
	return $agent ? $hasAgent && stripos($_SERVER['HTTP_USER_AGENT'], $agent) !== false : true;
}

/**
 * is_browser() 判断当前是否为浏览器访问
 * @param  string  $agent  [可选]客户端类型
 * @return boolean
 */
function is_browser($agent = ''){
	return is_agent($agent) && !is_curl() && !empty($_SERVER['HTTP_ACCEPT']) && !empty($_SERVER['HTTP_CONNECTION']) && (strtolower($_SERVER['HTTP_CONNECTION']) == 'keep-alive' || is_proxy());
}

/**
 * is_mobile() 判断当前是否为手机浏览器访问
 * @param  string  $agent  [可选]客户端类型
 * @return boolean
 */
function is_mobile($agent = ''){
	if(!is_browser()) return false;
	if(!$agent) return preg_match('/Android|BlackBerry|BB|PlayBook|iPhone|iPad|iPod|Windows\sPhone|IEMobile/i', $_SERVER['HTTP_USER_AGENT']);
	return is_browser($agent);
}

/**
 * is_ajax() 判断当前是否为 AJAX 请求，需要客户端发送 X-Requested-With: XMLHttpRequest 请求头
 * @return boolean
 */
function is_ajax(){
	return is_browser() && isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == 'xmlhttprequest';
}

/**
 * is_curl() 判断当前是否为 CURL 请求
 * @return boolean
 */
function is_curl(){
	return is_agent() && !empty($_SERVER['HTTP_ACCEPT']) && (empty($_SERVER['HTTP_USER_AGENT']) || stripos($_SERVER['HTTP_USER_AGENT'], 'curl/') === 0 || empty($_SERVER['HTTP_CONNECTION']));
}

/**
 * is_post() 判断当前是否为 POST 请求
 * @return boolean
 */
function is_post(){
	return is_agent() && $_SERVER['REQUEST_METHOD'] == 'POST';
}

/**
 * is_get() 判断当前是否为 GET 请求
 * @return boolean 
 */
function is_get(){
	return is_agent() && $_SERVER['REQUEST_METHOD'] == 'GET';
}

/**
 * is_ssl() 判断当前请求是否使用 SSL 协议
 * @return boolean
 */
function is_ssl(){
	return isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on';
}

/**
 * is_proxy_server() 判断应用程序是否运行为代理服务器
 * @return boolean
 */
function is_proxy_server(){
	return !empty($_SERVER['HTTP_PROXY_CONNECTION']) || (!empty($_SERVER['REQUEST_URI']) && (stripos($_SERVER['REQUEST_URI'], 'http://') === 0 || stripos($_SERVER['REQUEST_URI'], 'https://') === 0));
}

/**
 * is_proxy() 判断客户端是否通过代理服务器访问
 * @return boolean
 */
function is_proxy(){
	return !empty($_SERVER['HTTP_X_FORWARDED_FOR']) || !empty($_SERVER['HTTP_VIA']);
}

/**
 * redirect() 设置网页重定向
 * @param  string|int  $url   重定向 URL，特殊值 0(当前页)，-1(上一页)
 * @param  integer     $code  [可选]状态号 301 或 302(默认)
 * @param  integer     $time  [可选]等待时间，默认 0
 * @param  string      $msg   [可选]跳转提示
 * @return null
 */
function redirect($url, $code = 302, $time = 0, $msg = ''){
	if(!is_agent()) return;
	if(ob_get_length()) ob_end_clean(); //清空缓冲区
	if(!$url) $url = url();
	elseif($url == -1) $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url();	
	if(!headers_sent()){
		if($code == 301) header('HTTP/1.1 301 Moved Permanently'); //永久重定向
		else header('HTTP/1.1 302 Moved Temporarily');
		header($time ? "Refresh: $time; URL=$url" : "Location: $url"); //使用 HTTP 响应头进行跳转
	}else{
		echo "<meta http-equiv=\"Refresh\" content=\"$time; URL=$url\">\n"; //使用 HTML 元信息跳转
	}
	exit($msg);
}

/**
 * set_query_string() 设置 URL 查询字符串，并重新加载页面
 * @param  string|array $key   参数名，也可设置为关联数组同时设置多个参数
 * @param  string       $value 参数值，设置为 null 或 false 则清除该参数
 */
function set_query_string($key, $value){
	if(!is_array($key)) $key = array($key => $value);
	foreach ($key as $k => $v){
		if($v === null || $v === false){
			unset($_GET[$k]); //删除参数
		}else{
			$_GET[$k] = $v; //设置参数
		}
	}
	$url = explode('?', url());
	redirect($url[0].'?'.http_build_query($_GET));
}

/**
 * set_content_type() 设置文档类型和编码
 * @param string $type     文档类型
 * @param string $encoding [可选]编码，默认 UTF-8
 */
function set_content_type($type, $encoding = 'UTF-8'){
	if(!headers_sent()){
		header("Content-Type: $type; charset=$encoding"); //在响应头中设置
	}else{
		echo "<meta http-equiv=\"content-type\" content=\"$type; charset=$encoding\">\n"; //在元信息中设置
	}
}

/**
 * url() 获取当前 URL 地址(不包括 # 及后面的的内容)
 * @return string URL 地址
 */
function url(){
	if(!is_agent()) return false;
	if(is_proxy_server()) return $_SERVER['REQUEST_URI']; //代理地址
	$protocol = strstr(strtolower($_SERVER['SERVER_PROTOCOL']), '/', true);
	$protocol .= is_ssl() ? 's' : ''; //SSL 使用 https
	return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}

/**
 * get_client_ip() 获取客户端 IP 地址
 * @param  boolean $strict [可选]严格模式，排除 192.168 等私有 IP 地址, 默认 false
 * @return string          客户端 IP 地址
 */
function get_client_ip($strict = false){
	$ip = false;
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){ //从代理地址中获取 IP
		$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		$ip = trim($ips[0]);
	}else if(isset($_SERVER['HTTP_CLIENT_IP'])){
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}else if(isset($_SERVER['REMOTE_ADDR'])){
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	if($ip && $strict){ //filter invalid ip
		$ip = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}
	return $ip;
}
function_alias('get_client_ip', 'get_agent_ip');

/**
 * array2xml() 将数组转换为 XML 结构数据
 * @param  array   $array    数组
 * @param  boolean $cdata    [可选]使用 CDATA 包裹值，默认 false；当值中包含标签名时，必须设置为 true。 
 * @return string            XML 文档
 */
function array2xml(array $array, $cdata = false){
	$xml = simplexml_load_string('<xml/>'); //创建 XML 对象
	$createXML = function($xml, $arr) use (&$createXML, $cdata){ //use 匿名函数自身的引用，就可以在内部进行递归运算
		foreach($arr as $key => $value){
			if(is_assoc($value)){ //处理关联数组
				$child = $xml->addChild($key);
				$createXML($child, $value); //递归
			}elseif(is_array($value)){ //处理索引数组
				foreach ($value as $item) {
					$createXML($xml, array($key=>$item)); //索引数组使用相同的键名
				}
			}elseif($cdata){
				$dom = dom_import_simplexml($xml->addChild($key));
				$doc = $dom->ownerDocument;
				$dom->appendChild($doc->createCDATASection($value)); //添加包含 CDATA 的节点
			}else{
				$xml->addChild($key, $value); //添加子节点
			}
		}
	};
	$createXML($xml, $array);
	return html_entity_decode($xml->saveXML());
}

/**
 * xml2array() 将 XML 数据转换为关联数组
 * @param  string $xml    XML 数据
 * @return array          转换后的数组
 */
function xml2array($xml){
	$obj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
	return object2array($obj);
}

if(extension_loaded('curl')):
/**
 * curl() 进行远程 HTTP 请求，需要开启 CURL 模块
 * @param array|string $options 设置请求的参数(数组)或者请求的 URL 地址；
 *                              也可以设置为一个数组包含多个 URL 地址，或者多个请求参数，来进行批处理请求
 *                              请求参数可以包含下面这些项目：
 *                              [url] => 远程请求地址
 *                              [method] => 请求方式: POST 或 GET(默认);
 *                              [data] => POST 数据, 支持关联数组、索引数组、URL 查询字符串以及原始 POST 数据；
 *                                        要发送文件，需要在文件名前面加上 @ 前缀，兼容 PHP 5.5.0+；
 *                                        可选在文件名后加 ;type={Mime-Type} 来设置文件的 MIME 类型
 *                              [cookie] => 发送 Cookie, 支持关联数组、索引数组和 Cookie 字符串
 *                              [referer] => 来路页面
 *                              [userAgent] => 客户端信息
 *                              [requestHeaders] => 请求头部信息，支持索引数组和关联数组
 *                              [followLocation] => 跟随跳转，设置数值为最大跳转次数，默认 0
 *                              [autoReferer] => 跳转时自动设置来路页面，默认 true
 *                              [sslVerify] => SSL 安全验证，默认 false
 *                              [proxy] => 代理服务器(格式: 8.8.8.8:80)
 *                              [clientIp] => 原始客户端 IP，当 curl 用来充当代理服务器时使用
 *                              [timeout] => 设置超时，默认 5(秒)
 *                              [onlyIpv4] => 只解析 IPv4，默认 true
 *                              [username] => HTTP 访问认证用户名
 *                              [password] => HTTP 访问认证密码
 *                              [charset] => 目标页面编码
 *                              [convert] => 转换为指定的编码
 *                              [parseJSON] => 解析 JSON，true 始终解析，false 始终不解析，默认自动解析
 *                              [decodeUnicode] => 解析 Unicode 字符串，默认 false
 *                              [success] => 请求成功时的回调函数
 *                              [error] => 请求失败时的回调函数
 *                              [extra] => 其他 CURL 选项参数，设置为一个数组
 * @param  int         $wait    批处理请求时等待前一个处理完成的超时秒数，默认 0，即异步并行处理
 * @return string               返回请求结果，结果是字符串
 */
function curl($options, $wait = false){
	$curl = curl_version();
	$curlData = array(); //请求结果数据
	$curlInfo = array(); //请求信息
	/* 定义默认的参数 */
	$defaults = array(
		'url'=>'',
		'method'=>'GET',
		'data'=>'',
		'cookie'=>'',
		'referer'=>'',
		'userAgent'=>'curl/'.$curl['version'],
		'requestHeaders'=>array(),
		'followLocation'=>0,
		'autoReferer'=>true,
		'sslVerify'=>false,
		'proxy'=>'',
		'clientIp'=>'',
		'timeout'=>5,
		'onlyIpv4'=>true,
		'username'=>'',
		'password'=>'',
		'charset'=>'',
		'convert'=>'',
		'parseJSON'=>null,
		'decodeUnicode'=>false,
		'success'=>null,
		'error'=>null,
		'extra'=>array()
		);
	if(!is_array($options)) $options = array('url'=>$options);
	$requests = !is_assoc($options) ? $options : array($options); //批处理请求
	$mh = curl_multi_init();
	foreach ($requests as $i => $options) {
		if(!is_array($options)) $options = array('url'=>$options);
		$requests[$i] = $options = array_merge($defaults, $options); //合并选项
		extract($options);
		$ch[$i] = curl_init($url); //初始化 CURL
		if($data && is_array($data) && !is_assoc($data)){
			$data = explode_assoc(implode('&', $data), '&', '=');
		}
		if(strtolower($method) == 'post'){ //POST 请求
			$createFile = function_exists('curl_file_create'); //PHP 5.5.0 起使用 CURLFile 上传文件
			if(is_array($data)){
				foreach ($data as $key => &$value) {
					if($value[0] == "@"){ //处理文件上传
						$file = ltrim($value, '@');
						if(!strpos($file, ';type=')){
							if(function_exists('mime_content_type') && $mime = mime_content_type($file)){ //获取 MIME 类型
								$value = $createFile ? curl_file_create($file, $mime) : '@'.$file.';type='.$mime; //设置 MIME 类型
							}elseif($createFile){
								$value = curl_file_create($file); //创建 CURLFile 对象
							}
						}elseif($createFile){
							$file = strstr($file, ';', true) ?: $file;
							$mime = substr($value, strpos($value, ';type=')+6); //获取 MIME 类型
							$value = curl_file_create($file, $mime);
						}
					}
				}
			}
			curl_setopt($ch[$i], CURLOPT_POST, 1);
			curl_setopt($ch[$i], CURLOPT_POSTFIELDS, $data);
		}else{ //GET 请求
			if($data) $url .= (strpos($url, '?') ? '&' : '?').(is_array($data) ? http_build_query($data) : $data);
		}
		if($cookie && is_array($cookie)){ //组合 Cookie
			$cookie = is_assoc($cookie) ? implode_assoc($cookie, '=', '; ') : implode('; ', $cookie);
		}
		if(!is_assoc($requestHeaders)){ //将请求头转换为关联数组
			foreach ($requestHeaders as $key => $value){
				if(is_numeric($key)){
					unset($requestHeaders[$key]);
					$i = strpos($value, ':');
					$key = substr($value, 0, $i);
					$value = ltrim(substr($value, $i+1));
					$requestHeaders[$key] = $value;
				}
			}
		}
		if($proxy && empty($requestHeaders['Proxy-Connection'])){
			$requestHeaders['Proxy-Connection'] = 'close'; //代理连接设置为 close
		}
		if($clientIp){
			$requestHeaders['Client-IP'] = $clientIp; //设置客户端 IP
			if(empty($requestHeaders['X-Forwarded-For']))
				$requestHeaders['X-Forwarded-For'] = $clientIp; //设置代理
		}
		foreach ($requestHeaders as $key => $value){
			$requestHeaders[] = "$key: $value"; //组合请求头
			unset($requestHeaders[$key]);
		}
		//设置请求参数
		curl_setopt($ch[$i], CURLOPT_HEADER, true);
		curl_setopt($ch[$i], CURLINFO_HEADER_OUT, true);
		curl_setopt($ch[$i], CURLOPT_FOLLOWLOCATION, $followLocation ? true : false);
		curl_setopt($ch[$i], CURLOPT_MAXREDIRS, $followLocation);
		curl_setopt($ch[$i], CURLOPT_AUTOREFERER, $autoReferer);
		curl_setopt($ch[$i], CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch[$i], CURLOPT_SSL_VERIFYHOST, 2);
		if($sslVerify) curl_setopt($ch[$i], CURLOPT_SSL_VERIFYPEER, 2);
		else curl_setopt($ch[$i], CURLOPT_SSL_VERIFYPEER, false);
		if($cookie) curl_setopt($ch[$i], CURLOPT_COOKIE, $cookie);
		if($referer) curl_setopt($ch[$i], CURLOPT_REFERER, $referer);
		if($userAgent) curl_setopt($ch[$i], CURLOPT_USERAGENT, $userAgent);
		if($proxy) curl_setopt($ch[$i], CURLOPT_PROXY, $proxy);
		if($onlyIpv4) curl_setopt($ch[$i], CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		if($requestHeaders) curl_setopt($ch[$i], CURLOPT_HTTPHEADER, $requestHeaders);
		if($username) curl_setopt($ch[$i], CURLOPT_USERPWD, $username.':'.$password);
		if($extra) curl_setopt_array($ch[$i], $extra); //设置额外参数
		curl_multi_add_handle($mh, $ch[$i]); //添加批处理句柄
	}
	do{
		curl_multi_exec($mh, $running); //执行批处理请求
		if($wait) curl_multi_select($mh, $wait);
	}while($running);
	foreach ($requests as $i => $options) {
		extract($options);
		$data = curl_multi_getcontent($ch[$i]); //取得结果
		$curlInfo[$i] = curl_getinfo($ch[$i]); //获取请求信息
		$curlInfo[$i]['error'] = ''; //保存错误的字段
		if(isset($curlInfo[$i]['request_header'])){
			$curlInfo[$i]['request_headers'] = parse_header($curlInfo[$i]['request_header']); //解析请求头
			unset($curlInfo[$i]['request_header']);
		}else{
			$curlInfo[$i]['request_headers'] = array();
		}
		if(curl_errno($ch[$i]) || !$data){
			$curlInfo[$i]['error'] = curl_error($ch[$i]); //获取错误信息
		}
		curl_multi_remove_handle($mh, $ch[$i]); //移除批处理句柄
		curl_close($ch[$i]); //关闭句柄
		if($data){
			if(!$charset){ //自动获取目标页面使用的编码
				if(preg_match('/.*charset=(.+)/i', $curlInfo[$i]['content_type'], $match)){
					$charset = $match[1];
				}else{
					$_data = str_replace(array('\'', '"', '/'), '', $data);
					$htmlRegex = '/<meta.*charset=(.+)>/iU'; //HTML 编码
					$xmlRegex = '/<\?xml.*encoding=(.+)\?>/iU'; //XML 编码
					if(preg_match($htmlRegex, $_data, $match) || preg_match($xmlRegex, $_data, $match)){
						$charset = strstr($match[1], ' ', true) ?: $match[1];
					}else{
						$charset = 'UTF-8'; //没有检测到编码则设置为 UTF-8
					}
				}
			}
			if(strtolower(str_replace('-', '', $charset)) != 'utf8')
				$data = iconv($charset, 'UTF-8', $data); //将数据转码为 UTF-8
			if($convert) $data = iconv('UTF-8', $convert, $data); //将数据转换为其他编码
			$header = substr($data, 0, $curlInfo[$i]['header_size']); //获取头部信息
			$data = substr($data, $curlInfo[$i]['header_size']); //获取主体数据
			if($decodeUnicode) $data = unicode_decode($data); //进行 Unicode 解码
			$curlInfo[$i]['response_headers'] = parse_header($header); //解析响应头
			if($curlInfo[$i]['http_code'] >= 400){ //400 以上的 HTTP 代码表示网页有错误
				$curlInfo[$i]['error'] = $data;
				$data = ""; //清空数据
				if(is_callable($error)){
					$_error = $error($curlInfo[$i]['error']); //执行失败回调函数
					if($_error !== null) $curlInfo[$i]['error'] = $_error; //保存返回的错误信息
				}
			}else{
				if($parseJSON || ($parseJSON === null && stripos($curlInfo[$i]['content_type'], 'application/json') !== false))
					$data = json_decode($data, true); //解析 JSON
				if(is_callable($success)){
					$_data = $success($data); //执行成功回调函数
					if($_data !== null) $data = $_data; //保存返回的数据
				}
			}
		}else{
			$curlInfo[$i]['response_headers'] = array();
		}
		$curlData[$i] = $data;
	}
	curl_multi_close($mh);
	if(count($curlData) == 1){ //处理单个请求
		$curlData = $curlData[0];
		$curlInfo = $curlInfo[0];
	}
	curl_info($curlInfo); //填充 CURL 信息
	return $curlData; //返回请求结果
}

/**
 * curl_info() 获取 CURL 请求的相关信息，需要运行在 curl() 函数之后
 * @param  string $key   [可选]设置要获取信息的键名
 * @return mixed         当未设置 $key 时返回所有数组内容，当设置 $key 时，返回对应的内容或者 false
 */
function curl_info($key = ''){
	static $info = array();
	if(is_array($key)){
		return $info = $key;
	}else{
		return $key === "" ? $info : (isset($info[$key]) ? $info[$key] : false);
	}
}

/**
 * curl_cookie_str() 获取 CURL 响应头中的 Cookie 字符串
 * @param  boolean $withSentCookie [可选]返回值包含发送的 Cookie(如果有)
 * @return string                  返回所有的 Cookie 字符串
 */
function curl_cookie_str($withSentCookie = false){
	$cookies = array();
	$curlInfo = curl_info();
	if(!$curlInfo) return false;
	if(is_assoc($curlInfo)) $curlInfo = array($curlInfo);
	foreach ($curlInfo as $i => $info) {
		$cookie = '';
		$reqHr = $info['request_headers'];
		$resHr = $info['response_headers'];
		if(!empty($resHr['Set-Cookie'])){
			if(is_string($resHr['Set-Cookie']))
				$resHr['Set-Cookie'] = array($resHr['Set-Cookie']);
			foreach ($resHr['Set-Cookie'] as &$value){
				$value = strstr($value, ';', true) ?: $value;
			}
			$cookie = join('; ', $resHr['Set-Cookie']);
		}
		if($withSentCookie && !empty($reqHr['Cookie'])){
			$cookie .= '; '.$reqHr['Cookie']; //加上发送的 Cookie
		}
		$cookies[] = $cookie;
	}
	return count($cookies) > 1 ? $cookies : (isset($cookies[0]) ? $cookies[0] : "");
}
endif;

/**
 * parse_header() 解析头部信息为关联数组
 * @param  string $str 头部信息字符串
 * @return array       解析后的数组
 */
function parse_header($str){
	$headers = explode("\n", trim(str_replace(array("\r\n", "\r"), "\n", $str), "\n")); //兼容行尾
	$_headers = array();
	$__headers = array();
	foreach($headers as $header){
		if($i = strpos($header, ':')){
			$key = substr($header, 0, $i);
			$value = trim(substr($header, $i+1));
			if(strpos($key, ' ')){
				$__headers[] = $header;
			}elseif(array_key_exists($key, $_headers)){ //如果一个参数多次使用，如 Set-Cookie
				if(!is_array($_headers[$key]))     //则将其保存为索引数组
					$_headers[$key] = array($_headers[$key]);
				$_headers[$key][] = $value;
			}else{
				$_headers[$key] = $value;
			}
		}else{
			$__headers[] = $header;
		}
	}
	return array_merge($__headers, $_headers);
}

/**
 * get_response_headers() 获取发送到客户端（或准备发送）的 HTTP 响应头信息
 * @param  string $name [可选]指定头信息名称
 * @return mixed        如果未设置 $name 参数，则返回所有头信息组成的关联数组；
 *                      如果设置了 $name 参数，则返回指定的头信息，没有则返回 false
 */
function get_response_headers($name = ''){
	$headers = parse_header(implode("\r\n", headers_list()));
	return $name ? (isset($headers[$name]) ? $headers[$name] : false) : $headers;
}

/**
 * get_request_headers() 获取全部 HTTP 请求头信息
 * @param  string $name [可选]指定头信息名称
 * @return mixed        如果未设置 $name 参数，则返回所有头信息组成的关联数组；
 *                      如果设置了 $name 参数，则返回指定的头信息，没有则返回 false
 */
function get_request_headers($name = ''){
	$headers = array();
	foreach ($_SERVER as $key => $value) {
		if(strpos($key, 'HTTP_') === 0){
			$key = explode("_", strtolower(substr($key, 5)));
			foreach ($key as &$v) {
				$v = ucfirst($v);
			}
			$key = implode('-', $key);
			$headers[$key] = $value;
		}
	}
	return $name ? (isset($headers[$name]) ? $headers[$name] : false) : $headers;
}

/**
 * session_retrieve() 重现会话
 * @param  string $id Session ID
 * @return bool
 */
function session_retrieve($id){
	if(!file_exists(session_save_path().'/sess_'.$id)) return false;
	session_id($id);
	@session_start();
	return true;
}

if(!function_exists('session_status')):
define('PHP_SESSION_DISABLED', 0);
define('PHP_SESSION_NONE', 1);
define('PHP_SESSION_ACTIVE', 2);
/**
 * session_status() 返回当前会话状态，该函数自 PHP5.4 起为内置函数
 * @return int      PHP_SESSION_DISABLED 会话是被禁用的
 *                  PHP_SESSION_NONE 会话是启用的，但不存在当前会话
 *                  PHP_SESSION_ACTIVE 会话是启用的，而且存在当前会话
 */
function session_status(){
	if(!extension_loaded('session')){
		return 0;
	}elseif(!file_exists(session_save_path().'/sess_'.session_id())){
		return 1;
	}else{
		return 2;
	}
}
endif;

if(!function_exists('hex2bin')):
/**
 * hex2bin() 转换十六进制字符串为二进制字符串，该函数自 PHP5.4 起为内置函数
 * @param  string $hex 十六进制字符串
 * @return string      ASCII 字符串
 */
function hex2bin($hex){
	if(strlen($hex) % 2) return false;
	foreach (str_split($hex, 2) as $v) {
		$bin[] = chr(hexdec($v));
	}
	return join('', $bin);
}
endif;

/** 
 * parse_cli_param() 解析 PHP 运行于 CLI 模式时传入的参数，支持的格式包括 --key value，-k value 以及 value
 * @param  array $argv 通常为 $_SERVER['agrv']
 * @return array       包含键值对 file=>当前运行文件, param=>(array)参数列表
 */
function parse_cli_param(array $argv, $i = 0, $isArg = false, $args = array()){
	if(!$argv) return false;
	$_i = 1;
	if(!$args){
		$args = array(
			'file'=>$argv[0], //当前运行文件名
			'param'=>array() //参数列表
			);
		$argv = array_slice($argv, 1);
	}
	if(!$argv) return $args;
	if(isset($argv[0][0]) && $argv[0][0] == '-'){
		if(isset($argv[0][1]) && $argv[0][1] != '-' && !isset($argv[0][2])){ //-k 短键名参数
			$key = $argv[0][1];
		}elseif(isset($argv[0][1]) && $argv[0][1] == '-' && isset($argv[0][2]) && $argv[0][2] != '-'){ //--key 长键名参数
			$key = ltrim($argv[0], '-');
		}else{ //无键名参数
			$value = $argv[0];
		}
	}elseif($argv[0] != ';'){ //无键名参数
		$value = $argv[0];
	}
	if(isset($key)){
		if(isset($argv[1]) && $argv[1][0] != '-' && $key[strlen($key)-1] != ';'){
			$value = $argv[1]; //参数值
			$_i = 2;
		}else{
			$value = '';
		}
	}
	if(!$isArg){
		$args['param'][$i] = array(
			'cmd'=>rtrim($value, ';'), //命令
			'args'=>array() //参数
			);
	}else{
		if(isset($key))
			$args['param'][$i]['args'][rtrim($key, ';')] = rtrim($value, ';');
		elseif($argv[0] != ';') //下一个命令开始
			$args['param'][$i]['args'][] = rtrim($value, ';');
	}
	$last = strlen($argv[$_i-1])-1;
	if(isset($argv[$_i-1][$last]) && $argv[$_i-1][$last] == ';'){ //多命令分句
		$i += 1;
		$isArg = false;
	}else{
		$isArg = true;
	}
	$argv = array_slice($argv, $_i);
	if($argv){
		return parse_cli_param($argv, $i, $isArg, $args); //递归计算
	}
	return $args;
}

/**
 * parse_cli_str() 解析命令行格式的字符串为数组
 * @param  string $str 输入字符串
 * @return array       解析后的命令
 */
function parse_cli_str($str){
	if(preg_match_all('/(["].+["].*)[\s]|(.+)[\s]|([\'].+)\b[\']/U', $str." ", $matches)){
		return array_map(function($v){
			$v = trim($v);
			if(substr_count($v, '"') == 2){ //使用双引号包裹内容
				$i1 = strpos($v, '"');
				$i2 = strrpos($v, '"');
				$v = substr($v, 0, $i1).substr($v, $i1+1, $i2-1).substr($v, $i2+1);
			}
			return $v;
		}, $matches[0]);
	}
	return false;
}

/**
 * get_cmd_encoding() 获取 Windows 命令行编码
 * @return string 编码号
 */
function get_cmd_encoding(){
	return PHP_OS == 'WINNT' ? 'CP'.trim(strstr(`chcp`, ': '), ": \r\n") : '';
}

/**
 * ping() 测试一个主机能否被连接
 * @param  string  $addr    主机地址
 * @param  boolean &$output [可选]将输出保存到该变量中
 * @return boolean
 */
function ping($addr, &$output = null){
	exec('ping '.(PHP_OS == 'WINNT' ? '-n': '-c')." 1 $addr", $output, $errno);
	return !$errno;
}

/**
 * object2array() 将对象转换为数组
 * @param  object $obj 对象
 * @return array       数组
 */
function object2array($obj){
	return json_decode(json_encode($obj), true);
}

/**
 * array2object() 将数组转换为对象
 * @param  array  $arr 数组
 * @return object      基本对象
 */
function array2object(array $arr){
	return json_decode(json_encode($arr));
}

if(extension_loaded('sockets')):
/**
 * get_local_ip() 获取本地 IP 地址
 * @return string 本机 IP 地址
 */
function get_local_ip(){
	$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	socket_connect($sock, "8.8.8.8", 53);
	socket_getsockname($sock, $name);
	socket_close($sock);
	return $name;
}
endif;

/**
 * doc() 获取一个函数、类或类方法的程序文档，支持命名空间和继承
 * @param  string  $name   [可选]函数名、类名或方法名，示例: 
 *                         doc('ping'), doc('user::getMe'), doc('foo\bar'), doc('template::')
 * @param  boolean $return [可选]将文档返回，默认 false，直接输出
 * @return string|null     文档内容或者 Null
 */
function doc($name = '', $return = false){
	$name = $_name = $name ?: __function__;
	$class = '';
	$isClass = false; //是否为类
	if($isMd = strpos($name, '::')){ //判断是否为方法
		$class = substr($name, 0, $isMd);
		$mdName = substr($name, $isMd+2);
		if($mdName){
			if(!method_exists($class, $mdName)){ //判断方法是否存在
				if(!$return) echo "Method $_name() doesn't exist.";
				return;
			}
		}else{
			$isMd = false;
			$isClass = true;
			if(!class_exists($class)){
				if(!$return) echo "Class $class doesn't exist.";
				return;
			}
		}
	}else{
		if(!function_exists($name)){ //判断函数是否存在
			if(!$return) echo "Function $_name() doesn't exist.";
			return;
		}
	}
	$hasNs = strrpos($name, '\\'); //是否有命名空间
	if($hasNs){ //处理命名空间
		$ns = str_replace('\\', '\\\\', substr($name, 0, $hasNs));
		$nsReg = '/namespace[\s]+'.ltrim($ns, '\\').'/i'; //命名空间定义格式
		$name = substr($name, $hasNs+1);
	}
	if($isMd || $isClass){ //处理类和类方法
		$i = strpos($name, '::');
		$class = substr($name, 0, $i);
		$name = $mdName;
		$classReg1 = '/class[\s]+'.ltrim($class, '\\').'/i'; //类定义格式
		$classReg2 = '/[\s]+extends[\s]+([\\\_a-zA-Z0-9]*)/i'; //继承
	}
	$doc = '';
	$includes = get_included_files(); //获取引入的所有文件
	$classReg = "/(\/\*\*[\s\S]*\/)[\r\n\sa-zA-Z]+class[\s]+".ltrim($class, '\\').'[\s\S]*\{/iU'; //类定义格式
	$funcReg = "/(\/\*\*[\s\S]*\/)[\r\n\sa-zA-Z]+function[\s]+".ltrim($name, '\\').'\([\s\S]*\)/iU'; //函数定义格式
	$getDoc = function($code) use ($classReg, $funcReg, $isClass, $isMd, $hasNs, &$getDoc){
		if((!$isClass && preg_match($funcReg, $code, $match)) || ($isClass && preg_match($classReg, $code, $match))){
			$declare = $match[0]; //定义函数/类/类方法的代码（包括 PHPDoc）
			$end = strrpos($declare, '*/');
			$declare = trim(substr($declare, $end+2), "\r\n"); //定义语句
			if(!$isMd && !$isClass){
				if((!$hasNs && stripos($declare, 'function') !== 0) || ($hasNs && stripos(trim($declare), 'function') !== 0)){
					$code = substr($code, strpos($code, $declare)+strlen($declare));
					return $getDoc($code);
				}
			}
			$match = $match[1];
			$start = strrpos($match, '/**'); //定位到文档开始位置
			$doc = substr($match, $start);
			$docs = explode("\n", $doc);
			foreach ($docs as &$line) {
				$line = trim($line); //去除文档每一行两端的空格
				if($line[0] == '*') $line = ' '.$line; //恢复对齐
			}
			return join("\n", $docs);
		}
	};
	foreach ($includes as $file) {
		$inZip = stripos($file, '.zip#'); //是否为 ZIP 中的文件
		$code = file_get_contents($inZip ? 'zip://'.$file : $file); //文件内容
		if($hasNs){ //处理命名空间
			if(!preg_match($nsReg, $code, $match))
				continue;
			else
				$code = strstr($code, $match[0]); //定位到命名空间位置
		}
		if($isMd || $isClass){ //处理类方法
			if(!preg_match($classReg1, $code, $match))
				continue;
			elseif($isMd){
				$code = strstr($code, $match[0]); //定位到类位置
				if(preg_match($classReg2, strstr($code, "\n", true), $match)){
					$parent = $match[1]; //父类
				}
			}
		}
		$doc = $getDoc($code);
		if($doc) break;
	}
	if(!$doc && isset($parent)){
		$doc = doc($parent.'::'.$mdName, true); //从父类获取方法文档
	}
	if($return){
		return $doc;
	}else{
		if($doc && is_browser() && !is_ajax()){
			$doc = '<pre>'.$doc.'</pre>';
		}
		echo $doc ?: ($isMd ? "Method $_name()" : ($isClass ? "Class ".rtrim($_name, '::') : "Function $_name()"))." doesn't have a documentation.";
	}
}

/**
 * encrypt() 加密一段数据
 * @param  string $data   待加密的数据
 * @param  string $key    密钥
 * @param  int    $expire [可选]过期时间，默认 0(秒)
 * @return string         加密后的数据
 */
function encrypt($data, $key, $expire = 0){
	$key = (string)$key;
	$klen = strlen($key);
	if(!$klen){ //密钥不能为空
		trigger_error("The second argument passed to encrypt() must not be empty.", E_USER_WARNING);
		return false;
	}
	$expire = sprintf('%010d', $expire ? $expire + time() : 0);
	$data = $key.$expire.(string)$data; //确保数据长度 >= 密钥长度
	$len = strlen($data);
	for ($i=$j=0,$str=''; $i < $len; $i++,$j++) { 
		if($j == $klen) $j=0;
		$str .= chr(ord($data[$i]) + ord($key[$j]));
	}
	return base64_encode($str);
}

/**
 * decrypt() 解密一段由 encrypt() 函数加密的数据
 * @param  string $data 待解密的数据
 * @param  string $key  密钥
 * @return mixed        解密后的数据，如果密文已过期，则返回 false
 */
function decrypt($data, $key){
	$key = (string)$key;
	$klen = strlen($key);
	if(!$klen){
		trigger_error("The second argument passed to decrypt() must not be empty.", E_USER_WARNING);
		return false;
	}
	$data = (string)$data;
	$data = base64_decode($data);
	$len = strlen($data);
	for ($i=$j=0,$str=''; $i < $len; $i++,$j++) { 
		if($j == $klen) $j=0;
		$str .= chr(ord($data[$i])-ord($key[$j]));
	}
	$expire = substr($str, $klen, 10);
	if($expire > 0 && $expire < time()){ //判断密文是否过期
		return false;
	}
	return substr($str, $klen+10);
}

/**
 * is_robot() 判断是否为机器人访问
 * @param  string  $spider [可选]爬虫名称
 * @return boolean
 */
function is_robot($spider = ''){
	if($spider)
		return is_agent($spider);
	else
		return preg_match("/bot|spider|crawl|slurp|sohu-search|lycos|robozilla/i", $_SERVER['HTTP_USER_AGENT']);
}

/**
 * extname() 获取一个文件的扩展名(始终小写)
 * @param  string $filename 指定文件名
 * @return string           文件扩展名
 */
function extname($filename){
	return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * http_digest_auth() 进行 HTTP 摘要认证
 * @param  array    $users          保存用户信息的关联数组，格式为 array('username' => 'password')
 * @param  callable $error_callback [可选]用户取消登录时触发的回调函数
 * @param  string   $realm          [可选]设置域信息
 * @param  array    &$digest        [可选]如果设置，将被填充为浏览器发送的摘要信息
 * @return string                   登录的用户名
 */
function http_digest_auth(array $users, $error_callback = null, $realm = '', &$digest = array()){
	$realm = $realm ?: 'HTTP Digest Authentication';
	if(empty($_SERVER['PHP_AUTH_DIGEST'])){
		$nonce = md5(uniqid()); //随机数
		$opaque = md5($realm);
		/** 发送摘要认证响应头 */
		header('HTTP/1.1 401 Unauthorized');
		header('WWW-Authenticate: Digest realm="'.$realm.'", qop="auth", nonce="'.$nonce.'", opaque="'.$opaque.'"');
		if(is_callable($error_callback)){
			$error_callback(); //用户取消登录则激活回调函数
		}else{
			exit("401 Unauthorized");
		}
	}else{
		$digest = array('username'=>'', 'realm'=>'', 'nonce'=>'', 'uri'=>'', 'response'=>'', 'opaque'=>'', 'qop'=>'', 'nc'=>'', 'cnonce'=>'');
		if(preg_match_all('/\w+=["\'].*["\']|\w+=[\w\d]+\b/U', $_SERVER['PHP_AUTH_DIGEST'], $matches)){
			foreach($matches[0] as $part){
				$part = trim($part);
				$i = strpos($part, "=");
				$key = substr($part, 0, $i);
				$value = trim(substr($part, $i+1), '"\'');
				if(isset($digest[$key])){
					$digest[$key] = $value; //获取摘要信息
				}
			}
		}
		foreach($digest as $key => $val){
			if(!$val || ($key == 'username' && !isset($users[$val]))){ //判断认证信息是否合法以及用户是否存在
				unset($_SERVER['PHP_AUTH_DIGEST']);
				return http_digest_auth($users, $error_callback, $realm, $digest); //重新认证
			}
		}
		$A1 = $digest['username'].':'.$realm.':'.$users[$digest['username']];
		$A2 = $_SERVER['REQUEST_METHOD'].':'.$digest['uri'];
		$expect = md5(implode(':', array( //认证预期值
				md5($A1),
				$digest['nonce'],
				$digest['nc'],
				$digest['cnonce'],
				$digest['qop'],
				md5($A2)
			)));
		if($expect != $digest['response']){ //预期值与客户端响应不同则说明认证失败
			unset($_SERVER['PHP_AUTH_DIGEST']);
			return http_digest_auth($users, $error_callback, $realm, $digest);
		}
		return $digest['username'];
	}
}

/**
 * load_config() 加载配置
 * @param  string $file 配置文件名，支持 php, ini, json 和 xml
 * @return array        配置构成的多维数组，加载失败则返回 false
 */
function load_config($file){
	$ext = extname($file);
	if($ext == 'ini') //载入 ini
		return parse_ini_file($file) ?: false;
	elseif($ext == 'json') //载入 json
		return json_decode(file_get_contents($file)) ?: false;
	elseif($ext == 'xml') //载入 XML
		return xml2array(file_get_contents($file)) ?: false;
	else //载入 php
		return include($file) ?: false;
}

/**
 * path_starts_with() 判断一个路径是否以指定的字符串开头，在 Windows 中不区分大小写
 * @param  string $path 待检测的路径
 * @param  string $find 查找的字符串
 * @return boolean
 */
function path_starts_with($path, $find){
	return PHP_OS == 'WINNT' ? stripos($path, $find) === 0 : strpos($path, $find) === 0;
}