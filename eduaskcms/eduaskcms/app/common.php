<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

use app\common\utility\Hash;
use app\common\utility\TpText;

##公共代码
define('APP_CACHE_DIR' , APP_PATH . 'common' . DS . 'appcache' . DS);

$GLOBALS['Model']       = read_file_cache('Model');
$GLOBALS['Model_title'] = Hash::combine($GLOBALS['Model'], '{n}.model','{n}.cname');
$GLOBALS['Model_map']   = Hash::combine($GLOBALS['Model'], '{n}[is_menu=1].model', '{n}[is_menu=1].cname');


/**
 * 打印输出，可用于调试
 */
function pr($var)
{
	if (config('app_debug')) {
		$template = PHP_SAPI !== 'cli' ? '<pre>%s</pre>' : "\n%s\n";
		printf($template, print_r($var, true));
	}
}


function U($request=array(),$query=array(),$abs=false)
{
    if (func_num_args() == 2 && is_bool(func_get_arg(1))) {
        $query=array() ;$abs = func_get_arg(1) ;
    }
    
    ##当前模块
    $module  = isset($request['module']) ? strtolower($request['module']) : $GLOBALS['controller']->params['module'];
    if(!in_array($module,config('allow_module_list'))) $module = config('default_module');
    if($module ==  config('default_module'))  $module = null;
    unset($request['module']);
    
    ##当前控制器
    $controller  = isset($request['controller']) ? ucfirst($request['controller']) : $GLOBALS['controller']->params['controller'] ;
    unset($request['controller']);
    
    ##当前方法 
    $action = isset($request['action']) ? strtolower($request['action']) : $GLOBALS['controller']->params['action'] ;
    unset($request['action']);
    
    ##mian
    $main = sprintf('%s%s%s',$module ? $module.'/':'',$controller.'/',$action);
    
    ##去除掉某些参数名
    if ($request['remove']) {
        $request['remove'] = Hash::normalize((array)$request['remove']) ;
        $request = array_diff_key($request,$request['remove']);
        unset($request['remove']) ;
    }
    
    ##$request
    if(!is_array($query)) $query = explode('&',$query) ;
    $request = array_diff_key($request,$query);
    
    ##$query_arr
    if($query){
        foreach((array)$query as $name=>$value)    {
            if(is_array($value)) $value =  json_encode($value) ;
            $query_arr[] = "{$name}={$value}";
        }
    }
    return  html_entity_decode(sprintf("%s%s%s",$abs ? $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME']:'' ,url($main,$request),$query_arr ?'?'.implode('&',$query_arr) :''));
}

/**
* Url生成
* @param string        $url 路由地址
* @param string|array  $vars 变量
* @param bool|string   $suffix 生成的URL后缀
* @param bool|string   $domain 域名
* @return string
*/

function purl($plugin = null, $url = '', $vars = '',$suffix = true, $domain = false) {
    if ($plugin === null && isset($GLOBALS['plugin_params'])) {
        $plugin = $GLOBALS['plugin_params']['plugin'];
    }
    $url = 'plugin/replace_holder/' . parse_name($plugin) . '-' . str_replace('/', '-', $url);
    return str_replace('replace_holder/', '', url($url, $vars, $suffix, $domain));
}

/**
 *写入APP缓存
 */
function write_file_cache($name, $value)
{
	$exists=file_exists(APP_CACHE_DIR . $name . '.php');
	file_put_contents(APP_CACHE_DIR . $name . '.php',"<?php\nreturn " . var_export($value, true) . "\n?>");
	if (!$exists) {
	   @chmod(APP_CACHE_DIR . $name . '.php',fileperms(APP_CACHE_DIR)&0xffff);
	}
}
/**
 *读取APP缓存
 */
function read_file_cache($name)
{
    if (is_file(APP_CACHE_DIR.$name.'.php')) {
        return @include(APP_CACHE_DIR.$name.'.php');
    } else {
        return [];
    }
}


/**
 * Merge a group of arrays
 */
function am()
{
	$r = array();
	$args = func_get_args();
	foreach ($args as $a) {
		if (!is_array($a)) {
			$a = array($a);
		}
		$r = array_merge($r, $a);
	}
	return $r;
}

/**
 * Recursively strips slashes from all values in an array
 *
 * @param array $values Array of values to strip slashes
 * @return mixed What is returned from calling stripslashes
 */
function stripslashes_deep($values)
{
	if (is_array($values)) {
		foreach ($values as $key => $value) {
			$values[$key] = stripslashes_deep($value);
		}
	} else {
		$values = stripslashes($values);
	}
	return $values;
}

/**
 * 对象 转 数组 *
 * @param object $obj 对象
 * @return array
 */
function object_to_array($obj)
{
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)object_to_array($v);
        }
    }
    return $obj;
}


function ucfirst_deep($string)
{
    if(!is_string($string)) return $string ;
    $strArr =  explode('_',$string);
    foreach($strArr as $str){
        $retStr .= ucfirst(strtolower($str)) ;
    }
    return $retStr ;
}

function pluginSplit($name, $dotAppend = false, $plugin = null)
{
	if (strpos($name, '.') !== false) {
		$parts = explode('.', $name, 2);
		if ($dotAppend) {
			$parts[0] .= '.';
		}
		return $parts;
	}
	return array($plugin, $name);
}

function string_insert($string, $value, $replace_bracket = true)
{
	$ret = TpText::insert($string, Hash::flatten((array)$value), array('before'=>'{', 'after'=>'}'));

	if ($replace_bracket) {
	   $ret = preg_replace('/\{[^\}]+\.[^\}]+\}/s', '', $ret);		   
	}
	return $ret;
}

function string_trim($string)
{
    if (is_string($string)) {
        return trim($string);
    } elseif (is_array($string)) {
        foreach ($string as &$str) {
            $str = trim($str);
        }
        return $string;
    } else {
        return trim(strval($string));
    }
}

function mb_json_encode($var)
{
	return preg_replace("#\\\u([0-9a-f]+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))",json_encode($var));
}

function staticCacheShallow($key)
{
	if (!isset($GLOBALS[$key]))
		$GLOBALS[$key]=read_file_cache($key);
	if (!is_array($GLOBALS[$key]))
		$GLOBALS[$key]=array();
	
	$args=func_get_args();
	if(func_num_args() == 1)return $GLOBALS[$key];
	array_shift($args);

	return Hash::get($GLOBALS[$key],implode('.',$args));
}

function staticCacheDeep($key, $level1 = null, $level2 = null) {
	if (!isset($GLOBALS[$key]))
		$GLOBALS[$key] = read_file_cache($key);
	if (!is_array($GLOBALS[$key]))
		$GLOBALS[$key] = array();
	
	if (func_num_args() == 1) {
		return $GLOBALS[$key];
	} elseif (func_num_args() == 2) {
		if (isset($level1)){
			if (array_key_exists($level1,$GLOBALS[$key]))
				return $GLOBALS[$key][$level1];
			else
				return $GLOBALS[$key]['list'][$level1];
		} else {
			return null;
		}
	} elseif (func_num_args() >= 3) {
		if(isset($level1) && isset($level2)){
			if(array_key_exists($level1,$GLOBALS[$key]))
				return $GLOBALS[$key][$level1][$level2];
			else
				return $GLOBALS[$key]['list'][$level1][$level2];
		} else {
			return null;
		}
	}
}

function staticCacheClosest($key, $id)
{
	$ret = array($id);	
	$childrens = staticCacheDeep($key,'children',$id);
	if (!$childrens) {
	   return $ret;
    } else {
		foreach ($childrens as $sub_id) {
			$ret=array_merge($ret,staticCacheClosest($key,$sub_id));
		}
	}
	return $ret;
}

function menu()
{
	return call_user_func_array('staticCacheDeep', array_merge(array('Menu'),func_get_args()));
}
function adminmenu()
{
	return call_user_func_array('staticCacheDeep', array_merge(array('AdminMenu'),func_get_args()));
}
function powertree()
{
	return call_user_func_array('staticCacheDeep', array_merge(array('PowerTree'),func_get_args()));
}
function managemenu()
{
	return call_user_func_array('staticCacheDeep',array_merge(array('ManageMenu'),func_get_args()));
}

function dict()
{
	return call_user_func_array('staticCacheShallow', array_merge(array('Dictionary'),func_get_args()));
}

function setting(){
	return call_user_func_array('staticCacheShallow', array_merge(array('Setting'),func_get_args()));
}

function getClosestMenus($menu_id)
{
	return call_user_func_array('staticCacheClosest', array_merge(array('Menu'),func_get_args()));
}

function getClosestFamily($key, $id , &$family = []){
    if (!isset($GLOBALS[$key])) {
        $GLOBALS[$key] = read_file_cache($key);
    }		
	if (!is_array($GLOBALS[$key])) {
        $family = $id ;
        return false ;
	}
    $family[] = $id;

    //$closest_id = array_keys(reset($GLOBALS[$key]))[0];    
    if ($GLOBALS[$key]['list'][$id]['parent_id']) {
        getClosestFamily($key, $GLOBALS[$key]['list'][$id]['parent_id'], $family);        
    } else {
        $family = array_reverse($family);
    }
}

function _getFirstLetter($str)
{
	$fchar = ord($str{0});
	if($fchar >= ord("A") and $fchar <= ord("z")) {
	   return strtoupper($str{0});
	}
	$s1 = @iconv("UTF-8","gb2312", $str);
	$s2 = @iconv("gb2312","UTF-8", $s1);
	if ($s2 == $str) {
	   $s = $s1;
    }
	else {
	   $s = $str;
    }
	$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
	if($asc >= -20319 and $asc <= -20284) return "A";
	if($asc >= -20283 and $asc <= -19776) return "B";
	if($asc >= -19775 and $asc <= -19219) return "C";
	if($asc >= -19218 and $asc <= -18711) return "D";
	if($asc >= -18710 and $asc <= -18527) return "E";
	if($asc >= -18526 and $asc <= -18240) return "F";
	if($asc >= -18239 and $asc <= -17923) return "G";
	if($asc >= -17922 and $asc <= -17418) return "H";
	if($asc >= -17417 and $asc <= -16475) return "J";
	if($asc >= -16474 and $asc <= -16213) return "K";
	if($asc >= -16212 and $asc <= -15641) return "L";
	if($asc >= -15640 and $asc <= -15166) return "M";
	if($asc >= -15165 and $asc <= -14923) return "N";
	if($asc >= -14922 and $asc <= -14915) return "O";
	if($asc >= -14914 and $asc <= -14631) return "P";
	if($asc >= -14630 and $asc <= -14150) return "Q";
	if($asc >= -14149 and $asc <= -14091) return "R";
	if($asc >= -14090 and $asc <= -13319) return "S";
	if($asc >= -13318 and $asc <= -12839) return "T";
	if($asc >= -12838 and $asc <= -12557) return "W";
	if($asc >= -12556 and $asc <= -11848) return "X";
	if($asc >= -11847 and $asc <= -11056) return "Y";
	if($asc >= -11055 and $asc <= -10247) return "Z";
	return null;
}

function getFirstLetter($zh, $limit=1)
{
	$ret = "";
	$s1 = @iconv("UTF-8","gb2312", $zh);
	$s2 = @iconv("gb2312","UTF-8", $s1);
	if ($s2 == $zh) { 
	   $zh = $s1;
    }
	for ($i = 0; $i < strlen($zh); $i++) {
		if(!$limit)break;
		$s1 = substr($zh,$i,1);
		$p = ord($s1);
		if ($p > 160) {
			$s2 = substr($zh,$i++,2);
			$new_charater = _getFirstLetter($s2);
			if(!$new_charater)continue;
			$ret .= _getFirstLetter($s2);
		} else {
			if(preg_match('/\s/',$s1))continue;
			$ret .= $s1;
		}
		$limit--;
	}
	return $ret;
}

function return_bytes($val) {
	$val = trim($val);
	$last = strtolower($val{strlen($val)-1});
	switch($last) {
	   // The 'G' modifier is available since PHP 5.1.0
	   case 'g':
		   $val *= 1024;
	   case 'm':
		   $val *= 1024;
	   case 'k':
		   $val *= 1024;
	}
	
	return $val;
}
    
function return_size($bytes, $separator='')
{
	//utility functions
	$kb = 1024;          //Kilobyte   
	$mb = 1024 * $kb;    //Megabyte   
	$gb = 1024 * $mb;    //Gigabyte   
	$tb = 1024 * $gb;    //Terabyte   

	if($bytes < $kb)   
		return $bytes."{$separator}B";   
	else if($bytes < $mb)   
		return round($bytes/$kb,2)."{$separator}KB";   
	else if($bytes < $gb)   
		return round($bytes/$mb,2)."{$separator}MB";   
	else if($bytes < $tb)   
		return round($bytes/$gb,2)."{$separator}GB";   
	else  
		return round($bytes/$tb,2)."{$separator}TB";   
}

function getDirSize($dir)
{
    $sizeResult = 0;
    $handle = opendir($dir);
    while (false !== ($FolderOrFile = readdir($handle)))
    { 
        if($FolderOrFile != "." && $FolderOrFile != "..") { 
            if (is_dir("$dir/$FolderOrFile")) { 
                $sizeResult += getDirSize("$dir/$FolderOrFile"); 
            } else { 
                $sizeResult += filesize("$dir/$FolderOrFile"); 
            }
        } 
    }
    closedir($handle);
    return $sizeResult;
}

function removeDir($dir)
{
    if (strpos($dir,'app') !== false) {
        return false ;
    }
    $handle = opendir($dir);
    while (false !== ($FolderOrFile = readdir($handle)))
    { 
        if($FolderOrFile != "." && $FolderOrFile != "..") { 
            if (is_dir("$dir/$FolderOrFile")) { 
                removeDir("$dir/$FolderOrFile"); 
            } else {
                @unlink("$dir/$FolderOrFile"); 
            }
        } 
    }
    closedir($handle);
}


function uploadFile($file_info, $folder='general', $filename=null, $forbidden_ext=array('exe', 'php', 'asp', 'bat', 'asa', 'vbs'))
{
	$GLOBALS['upload_file_error'] = '';
    
	switch ($file_info['error']) {
		case '0':
			break;
		case '1':
			$GLOBALS['upload_file_error'] = '文件大小超过了php.ini定义的upload_max_filesize值';
			break;
		case '2':
			$GLOBALS['bbot_upload_file_error'] = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
			break;
		case '3':
			$GLOBALS['upload_file_error'] = '文件上传不完全';
			break;
		case '4':
			$GLOBALS['upload_file_error'] = '无文件上传';
			break;
		case '6':
			$GLOBALS['upload_file_error'] = '缺少临时文件夹';
			break;
		case '7':
			$GLOBALS['upload_file_error'] = '写文件失败';
			break;
		case '8':
			$GLOBALS['upload_file_error'] = '上传被其它扩展中断';
			break;
		case '':
			$GLOBALS['upload_file_error'] = '上传表单错误';
			break;
		case '999':
		default:
			$GLOBALS['upload_file_error'] = '未知错误';
	}
	if (!empty($GLOBALS['upload_file_error'])) return false;	
	
	if (!is_uploaded_file($file_info['tmp_name'])) {
		if(!empty($GLOBALS['last_upload'])){
			$GLOBALS['last_upload'] = null;
			return $GLOBALS['last_upload'];
		}
		else{
			$GLOBALS['upload_file_error'] = '不是上传文件';
			return false;
		}
	}
    
	$ext = explode('.', $file_info['name']);
	$ext = strtolower(array_pop($ext));
	if (in_array($ext, $forbidden_ext)) {
		$GLOBALS['upload_file_error'] = "不允许上传后缀名为[$ext]的文件";
		return false;
	}

	$basepath = WWW_ROOT . 'upload' . DS . $folder . DS;
	if (!file_exists($basepath)) mkdir($basepath);
	$basepath = $basepath . date('Ym');
	if (!file_exists($basepath)) mkdir($basepath);
	
	if (empty($filename)) $filename = uniqid(mt_rand()) . '.' . $ext;
	else $filename = $filename . '.' . $ext;
	
	move_uploaded_file($file_info['tmp_name'], $basepath . DS . $filename);
    
	$GLOBALS['last_upload'] = 'upload/' . $folder . '/' . date('Ym') . '/' . $filename;

	return $GLOBALS['last_upload'];
}

function make_qrcode($text, $outfile = false, $logofile = false, $level = 'H', $size = 10, $margin = 2, $saveandprint = false, $resize = 0){ 
    $hphqrcode_file  =  \Env::get('root_path') . 'vendor' . DS .'phpqrcode-1.1.4' . DS . 'phpqrcode.php';
    include_once $hphqrcode_file;
    $filename = $defoutfile  = $outfile;
    $isLogo = false;
    if ($logofile && file_exists($logofile)) {
        $isLogo = true;
        if (!$outfile) $outfile = true ;
    }             
    $basepath = WWW_ROOT . 'upload' . DS . 'qrcode' . DS;
	if (!file_exists($basepath)) mkdir($basepath);
	$basepath = $basepath . date('Ym') . DS;
	if (!file_exists($basepath)) mkdir($basepath);
    $return_file  = uniqid(mt_rand()) . '.png';
    $filename = $basepath . $return_file;
    
    $qrcode = QRcode::png($text, $filename, $level, $size, $margin, $saveandprint);
    
    if ($isLogo && file_exists($filename)) {
        $QR = imagecreatefromstring(file_get_contents($filename));
        $logo = imagecreatefromstring(file_get_contents($logofile));
        $QR_width = imagesx($QR);
        $QR_height = imagesy($QR);
        $logo_width = imagesx($logo);
        $logo_height = imagesy($logo);
        $logo_qr_width = $QR_width / 5;
        $scale = $logo_width / $logo_qr_width;
        $logo_qr_height = $logo_height / $scale;
        $from_width = ($QR_width - $logo_qr_width) / 2;
        imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        imagepng($QR, $filename);
    }  else {
        $QR = imagecreatefrompng($filename);
    }
    
    if ($resize) {
        $image = \think\Image::open($filename);
        $image->thumb($resize, $resize)->save($filename);
        $QR = imagecreatefrompng($filename);
    }    
    if (!$defoutfile) {
        @unlink($filename);
        header("P3P: CP='IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT'");
        header('Content-type: image/x-png');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		imagepng($QR);
        exit;
    } 
    return 'upload/qrcode/' . date('Ym') . '/' . $return_file;
}

function furl($url)
{
    $url = trim($url);
    if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
        if ($url{0} == '/') $url = substr($url, 1);
        $url = $GLOBALS['controller']->absroot . $url;
    }
    return $url;
}


function get_verify_code($to, $length = 5, $expired = 300, $is_md5 = false)
{
    $charBase = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $shuffleBase=range(0,count($charBase)-1);
	shuffle($shuffleBase);	
    
	$ret = '';
	for($i = 0; $i < $length; $i++){
		$ret .= $charBase[$shuffleBase[$i]];
	}
    db('Verify')->insert([
        'to' => $to,
        'code' => $ret,
        'is_verify' => 0,
        'expired' => $expired > 0 ? time() + $expired : 0,
        'created' => date('Y-m-d H:i:s')
    ]);
    if ($is_md5) {
        return md5($ret);
    } else {
        return $ret;
    }
}

function check_verify_code($to, $code, $is_md5 = false)
{
    $codeData = db('Verify')->where([
        'is_verify' => 0,
        'to' => $to
    ])->order(['id' => 'DESC'])->find();
    
    if (empty($codeData)) {
        return false;
    }
    
    if ($codeData['expired'] <= time() && $codeData['expired'] > 0) {
        return false;
    }
    
    db('Verify')->where(['id' => $codeData['id']])->update(['is_verify' => 1]);
    if ($is_md5) {
        $verify_code = md5($codeData['code']);
    } else {
        $verify_code = $codeData['code'];
    }
    return !!$verify_code == $code;
}

function send_email($templateVar, $emailNumbers, $templateParam = [])
{
    $template = db('Email')->where(['vari' => $templateVar])->find();
    if (empty($template)) {
        return '标识为[' . $templateVar . ']的邮件模板不存在';
    }
    
    $auth  = new \app\common\helper\Auth();
    $logined = $auth->user();
    //准备变量
    $replace = array_merge([
        'date' => date('Y-m-d'),
        'datetime' => date('Y-m-d H:i:s'),
        'site_title' => setting('site_title'),
        'corp_title' => setting('corp_title'),
        'tel' => setting('tel'),
        'address' => setting('address'),
        'email' => setting('email'),
        'url' => $GLOBALS['controller']->absroot,
        'username' => $logined['username'],
        'nickname' => $logined['Member']['nickname'],
        'truename' => $logined['Member']['truename'],
        'headimg' => $logined['Member']['headimg']
    ], $templateParam);
    $real_replace = [];
    foreach ($replace as $key => $value) {
        if (substr($key, 0, 1) != '$') {
            $real_replace['$' . $key] = $value;
        } else {
            $real_replace[$key] = $value;
        }
    }   
    
    //变量替换 
    $template['content'] = strtr($template['content'], $real_replace);
        
    settype($emailNumbers, 'array');
    
    if (PHP_VERSION > 5.5) {
        $email_object  = new \app\common\api\SendMailer5();
    } else {
        $email_object  = new \app\common\api\SendMailer5();
    }
    
    $result = $email_object->send($emailNumbers, $template['email_title'], $template['content'], $template['fromname'] ? $template['fromname'] : '', $template['file'] ? $template['file'] : '');
    
    return $result;
}

/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 * @param string  $name 字符串
 * @param integer $type 转换类型
 * @param bool    $ucfirst 首字母是否大写（驼峰规则）
 * @return string
 */
function parse_name($name, $type = 0, $ucfirst = true)
{
    if ($type) {
        $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
            return strtoupper($match[1]);
        }, $name);

        return $ucfirst ? ucfirst($name) : lcfirst($name);
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}
/**
 * send http request
 * @param  array $rq http请求信息
 *                   url        : 请求的url地址
 *                   method     : 请求方法，'get', 'post', 'put', 'delete', 'head'
 *                   data       : 请求数据，如有设置，则method为post
 *                   header     : 需要设置的http头部
 *                   host       : 请求头部host
 *                   timeout    : 请求超时时间
 *                   cert       : ca文件路径
 *                   ssl_version: SSL版本号
 * @return string    http请求响应
 */
function send($rq) {
    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_URL, $rq['url']);
    switch (true) {
        case isset($rq['method']) && in_array(strtolower($rq['method']), array('get', 'post', 'put', 'delete', 'head')):
            $method = strtoupper($rq['method']);
            break;
        case isset($rq['data']):
            $method = 'POST';
            break;
        default:
            $method = 'GET';
    }
    $header = isset($rq['header']) ? $rq['header'] : array();
    $header[] = 'Method:' . $method;
    
    $header[] = "User-Agent:Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)";
    isset($rq['host']) && $header[] = 'Host:'.$rq['host'];
    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $method);
    isset($rq['timeout']) && curl_setopt($curlHandle, CURLOPT_TIMEOUT, $rq['timeout']);
    isset($rq['data']) && in_array($method, array('POST', 'PUT')) && curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $rq['data']);
    
    $ssl = substr($rq['url'], 0, 8) == "https://" ? true : false;
    if( isset($rq['cert'])){
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER,true);
        curl_setopt($curlHandle, CURLOPT_CAINFO, $rq['cert']);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST,2);
        if (isset($rq['ssl_version'])) {
            curl_setopt($curlHandle, CURLOPT_SSLVERSION, $rq['ssl_version']);
        } else {
            curl_setopt($curlHandle, CURLOPT_SSLVERSION, 4);
        }
    }else if( $ssl ){
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER,false);   //true any ca
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST,1);       //check only host
        if (isset($rq['ssl_version'])) {
            curl_setopt($curlHandle, CURLOPT_SSLVERSION, $rq['ssl_version']);
        } else {
            curl_setopt($curlHandle, CURLOPT_SSLVERSION, 4);
        }
    }
    $return['content'] = curl_exec($curlHandle);
    $return['result'] = curl_getinfo($curlHandle);    
    curl_close($curlHandle);
    return $return;
}

function isJsonValidate($string) { 
    /*json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
    */
    return !!preg_match('/^(\[|\{).*(\}|\])$/', $string);
}

function getMenuData($menu_id, $limit = 4, $options = []) {
    $menu_id = intval($menu_id);
    if (!$menu_id || $menu_id <= 0) {
        return [];
    }
    $modelName = menu($menu_id, 'type');
    $limit = intval($limit);
    if (empty($modelName) || $limit < 1) {
        return [];
    }
    $queryModel = model($modelName);
    $where = $options['where'] ? $options['where'] : [];
    if ($options['family']) {
        $where['menu_id'] = ['menu_id', 'IN', getClosestMenus($menu_id)];
    } else {
        $where['menu_id'] = $menu_id;
    }
    if ($queryModel->form['is_verify'] && setting('is_verify') && !$where['is_verify']) {
        $where['is_verify'] = 1;
    }    
    $contain = $options['contain'] ? $options['contain'] : [];
    $field = $options['field'] ? $options['field'] : [$queryModel->getPk(), $queryModel->display];
    if (!empty($contain)) {
        $contain = Hash::normalize($contain);
        foreach ($contain as $assocModel => $assocInfo) {
            if ($queryModel->assoc[$assocModel]['type'] == 'belongsTo') {
                ##belongsTo 关联，决解如果没有关联字段，将不会自动with关联
                $foreignKey = $assocInfo['foreignKey'] ? $assocInfo['foreignKey'] : \think\Loader::parseName($assocModel) . '_id';
                if (!in_array($foreignKey, $field)) {
                    $field[] = $foreignKey;
                }
            }
        }
    }
    $order = $options['order'] ? $options['order'] : [];
    if (empty($order)) {
        if ($queryModel->form['list_order']) {
            $order['list_order'] = 'DESC';
        }
        $order['id'] = 'DESC';
    }
    $type = $options['type'] && in_array($options['type'], ['find', 'select']) ? $options['type'] : 'select';
    $data = $queryModel->field($field)->with($queryModel->parseWith($contain))->where($where)->order($order)->limit($limit)->$type();
    return !empty($data) ? $queryModel->getArray($data) : []; 
}

function getAdData($vari, $limit = 0) {
    
    $ap_position_moble  = model('AdPosition');
          
    $ad[$vari] = $ap_position_moble->where(['vari' => $vari])->field(array_keys($ap_position_moble->form))->find();
    if ($ad[$vari]) {
        $ad[$vari] = $ad[$vari]->getArray();
        $ad_model  = model('Ad');
        
        if ($limit && $ad[$vari]['limit'] && $limit > $ad[$vari]['limit']) { 
            $limit = $ad[$vari]['limit'];
        } else {
            if ($limit == 0) {
                $limit = $ad[$vari]['limit'];
            }
        }            
        $ads = $ad_model->field(array_keys($ad_model->form))->where(['ad_position_id' => $ad[$vari]['id'], 'is_verify' => 1])->order(['list_order' => 'DESC', 'id' => 'DESC'])->limit($limit)->select();
        if ($ads) {
            $ads = $ad_model->getArray($ads);
            foreach ($ads as &$each) {
                if (!$each['thumb']) {
                    $each['thumb'] = $each['image'];
                }                     
                if (!$each['mobile_image']) {
                    $each['mobile_image'] = $each['image'];
                }                    
                if(!$each['mobile_thumb']) {
                   $each['mobile_thumb']  = $each['mobile_image'];
                }
            }
        }
        $ad[$vari]['Ad'] = $ads;
        return $ad;
     } else {
        return [];
     }
}


##------------------------------------------##


##路由注册

foreach($GLOBALS['Model_map'] as $model=>$name){
    ##列表页路由:/article/show/menu_id/5  => /article/show/5
    \Route::any(parse_name($model) . '/show/:menu_id', $model.'/show', [], ['menu_id'=>'\d+']); 
    ##详情页路由:/article/view/id/5 => /article/5
    \Route::any(parse_name($model) . '/:id', $model.'/view', [], ['id'=>'\d+']);
}

##导航别名 /news.html=>article/show?menu_id=73
if(menu('alias')){
    ##think\Route::any('news','article/show?menu_id=73');
    foreach(menu('alias') as $id=>$alias){
        \Route::any($alias, parse_name(menu($id, 'type')) . '/show?menu_id=' . $id);
    }
}
