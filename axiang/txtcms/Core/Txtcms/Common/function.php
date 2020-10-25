<?php
//计数器
function num($key, $step=0){
    $key=md5($key.'_num');
    if (!_static($key)) {
		_static($key,0);
    }
    if (empty($step)){
        return _static($key);
    }else{
        _static($key,intval(_static($key))+ intval($step));
	}
}
//存储数据
function _static($key,$value=null){
	static $_name=array();
	if (empty($key))
        return $_name;
	if (is_null($value)){
		return isset($_name[$key]) ? $_name[$key] : null;
	}
	$_name[$key]=$value;
	return null;
}
/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip($type = 0) {
	$type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}
//生成url地址
function url($url='',$vars='',$suffix=true){
	// 解析URL
    $info   =  parse_url($url);
    $url    =  !empty($info['path'])?$info['path']:ACTION_NAME;
	// 解析参数
    if(is_string($vars)) { // aaa=1&bbb=2 转换成数组
        parse_str($vars,$vars);
    }elseif(!is_array($vars)){
        $vars = array();
    }
	if(isset($info['query'])) { // 解析地址里面参数 合并到vars
        parse_str($info['query'],$params);
        $vars = array_merge($params,$vars);
    }
	$depr = config('URL_PATH_DEPR');
	if($url){
		if('/' != $depr) { // 安全替换
			$url=str_replace('/',$depr,$url);
		}
		//解析分组、模块和操作
		$url        =   trim($url,$depr);
		$path       =   explode($depr,$url);
		$var        =   array();
		$var[config('ACTION_VAR')]       =   !empty($path)?array_pop($path):ACTION_NAME;
        $var[config('MODULE_VAR')]       =   !empty($path)?array_pop($path):MODULE_NAME;
		 if(config('APP_GROUP_LIST')) {
			if(!empty($path)) {
				$group=array_pop($path);
				$var[config('GROUP_VAR')]=$group;
			}else{
				if(GROUP_NAME != config('DEFAULT_GROUP')) {
					$var[config('GROUP_VAR')]=   GROUP_NAME;
				}
			}
		}
	}
	if(config('URL_MODEL') == 1) { // 普通模式URL转换
        $url=__APP__.'?'.http_build_query(array_reverse($var));
        if(!empty($vars)) {
            $vars   =   urldecode(http_build_query($vars));
            $url   .=   '&'.$vars;
        }
    }else{
		if(config('URL_MODEL')==2){
			$root=__APP__.'?';
		}else{
			$root=__ROOT__.'/';
		}
		$url=$root.implode($depr,array_reverse($var));
        if(!empty($vars)) { // 添加参数
            foreach ($vars as $var => $val){
                if('' !== trim($val)){
					$val=urlencode($val);
					$val=str_replace(array('%21','%40','%2C'),array('!','@',','),$val);//还原一些不影响地址的url参数
					$url .= $depr . $var . $depr . $val;
				}
            }	
        }
		if($suffix) {
            $suffix   =  $suffix===true?config('URL_PATH_SUFFIX'):$suffix;
            if($pos = strpos($suffix, '|')){
                $suffix = substr($suffix, 0, $pos);
            }
            if($suffix && '/' != substr($url,-1)){
                $url  .=  '.'.ltrim($suffix,'.');
            }
        }
	}
	return $url;
}
//调试输出
function dump($str,$isexit=false){
	ob_start();
	var_dump($str);
	$output = ob_get_clean();
	echo '<pre>'.$output.'</pre>';
	if($isexit) exit;
	return;
}
//连接数据库
function DB($name=''){
	return new Db(rtrim(TEMP_PATH,'/'),'Db',$name);
}
//导入类库
function import($class,$ext='.class.php'){
	static $_file=array();
	$class=str_replace(array('.', '#'), array('/', '.'), $class);
	$classfile=APPLIB_PATH.$class.$ext;
	if(isset($_file[$classfile])) return true;
	$_file[$class]=true;
	$class_strut=explode('/', $class);
    if(!class_exists(basename($class),false)) {
        return require_load($classfile);
    }
}
//实例化控制器
function action($name){
	static $_action = array();
	$suffix=config('DEFAULT_C_SUFFIX');
	$name=$suffix.'/'.$name.$suffix;
	if(isset($_action[$name]))  return $_action[$name];
	import($name);
	$class=basename($name);
	if(class_exists($class,false)) {
        $action=new $class();
        $_action[$name]=$action;
        return $action;
    }else {
        return false;
    }
}
//配置获取、设置
function config($name=null, $value=null) {
    static $_config = array();
	if (empty($name))
        return $_config;
	if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtolower($name);
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : null;
            $_config[$name] = $value;
            return;
        }
        $name = explode('.', $name);
        $name[0] = strtolower($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
        $_config[$name[0]][$name[1]] = $value;
        return;
    }
    if (is_array($name))
        return $_config = array_merge($_config, array_change_key_case($name));
    return null;
}
//友好的require
function require_load($file){
	static $_isload = array();
	if(!isset($_isload[$file])){
		$loadFile=dirname($file).'/'.basename($file);
		$loadFile=str_replace('\\','/',$loadFile);
		if(is_file($loadFile)){
			$_isload[$file]=true;
			require $loadFile;
		}else{
			$_isload[$file]=false;
		}
	}
	return $_isload[$file];
}
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
function xml_encode($data, $root='txtcms', $item='item', $attr='', $id='id', $encoding='utf-8') {
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
/**
 * 404处理 
 * 调试模式会抛异常 
 * 部署模式下面传入url参数可以指定跳转页面，否则发送404信息
 * @param string $msg 提示信息
 * @param string $url 跳转URL地址
 * @return void
 */
function _404($msg='',$url='') {
    APP_DEBUG && throw_exception($msg);
    if(empty($url) && config('URL_404_REDIRECT')) {
		$url=config('URL_404_REDIRECT');
    }
    if($url) {
        redirect($url);
    }else{
        send_http_status(404);
        exit;
    }
}
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
        302 => 'Moved Temporarily ', // 1.1
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
/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time=0, $msg='') {
    //多行URL地址支持
    $url        = str_replace(array("\n", "\r"), '', $url);
    if (empty($msg))
        $msg    = "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if (0 === $time) {
            header('Location: ' . $url);
        } else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    } else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $msg;
        exit($str);
    }
}
//返回相对路径
function relative_path($path){
	$path=str_replace('\\','/',$path);
	return str_replace(APP_ROOT,'/',$path);
}
//时间记录
function runTime($start,$end='',$dec=4) {
    static $_info       =   array();
    static $_mem        =   array();
    if(is_float($end)) { // 记录时间
        $_info[$start]  =   $end;
    }elseif(!empty($end)){ // 统计时间和内存使用
        if(!isset($_info[$end])) $_info[$end]       =  microtime(TRUE);
        if(MEMORY_LIMIT_ON && $dec=='m'){
            if(!isset($_mem[$end])) $_mem[$end]     =  memory_get_usage();
            return number_format(($_mem[$end]-$_mem[$start])/1024);          
        }else{
            return number_format(($_info[$end]-$_info[$start]),$dec);
        }       
            
    }else{ // 记录时间和内存使用
        $_info[$start]  =  microtime(TRUE);
        if(MEMORY_LIMIT_ON) $_mem[$start]           =  memory_get_usage();
    }
}
/**
 * 错误输出
 * @param mixed $error 错误
 * @return void
 */
function exception($error) {
    $e = array();
    if (APP_DEBUG) {
        //调试模式下输出错误信息
        if (!is_array($error)) {
            $trace          = debug_backtrace();
            $e['message']   = $error;
            $e['file']      = relative_path($trace[0]['file']);
            $e['line']      = $trace[0]['line'];
            ob_start();
            debug_print_backtrace();
            $e['trace']=ob_get_clean();
        } else {
            $e=$error;
        }
    } else {
        //否则定向到错误页面
        $error_page         = config('ERROR_PAGE');
        if (!empty($error_page)) {
            redirect($error_page);
        } else {
            if (config('SHOW_ERROR_MSG'))
                $e['message'] = is_array($error) ? $error['message'] : $error;
            else
                $e['message'] = config('ERROR_MESSAGE');
        }
    }
    // 包含异常页面模板
    include config('TMPL_EXCEPTION_FILE');
    exit;
}
function throw_exception($msg,$type='TxtcmsException', $code=0){
	if (class_exists($type, false)){
		throw new $type($msg, $code);
	}else{
        exception($msg);
	}
}