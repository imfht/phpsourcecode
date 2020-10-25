<?php
require(CP_PATH . 'lib/common.function.php');
require(CP_PATH . 'ext/template_ext.php');

//调试运行时间和占用内存
function debug($flag='sys', $end = true){
	static $arr =array();
	if( !$end ){
		$arr[$flag] = microtime(true); 
	} else if( $end && isset($arr[$flag]) ) {
		echo  '运行时间:' . round( (microtime(true) - $arr[$flag]), 6) . 'S---------内存使用:' . memory_get_usage()/1000 . 'KB'; 
	}
}

//保存配置
function save_config($app, $new_config = array()){
	if( !is_file($app) ){
		$file = BASE_PATH . 'apps/' . $app. '/config.php';
	}else{
		$file = $app;
	}
	
	if( is_file($file) ) {
		$config = require($file);
		$config = array_merge($config, $new_config);
	}else{
		$config = $new_config;
	}
	$content = var_export($config, true);
	$content = str_replace("_PATH' => '" . addslashes(BASE_PATH), "_PATH' => BASE_PATH . '", $content);

	if( file_put_contents($file, "<?php \r\nreturn " . $content . ';' ) ) {
		return true;
	}
	return false;
}

//复制文件夹
function copy_dir($src, $dst, $del = false) {
	if ($del && file_exists($dst)){
		return del_dir($dst);
	}
	if (is_dir($src)) {
		@mkdir($dst, 0777, true);
		$files = scandir($src);
		foreach ($files as $file){
			if ($file != "." && $file != "..") copy_dir("$src/$file", "$dst/$file");
		}
	}
	else if (file_exists($src)) copy($src, $dst);
}
//T-Team获取微秒时间
function mtime()
{
    list($s1, $s2) = explode(' ', microtime());
    return (float) sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
}
//T-Team添加随机数
function getcode($length = 5, $mode = 0)
{
    switch ($mode) {
        case '1':
            $str = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
            break;
        case '2':
            $str = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ-=[]\',./';
            break;
        case '3':
            $str = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ<>?:"|{}_+';
            break;
        default:
            $str = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
    }
    $result = '';
    $l = strlen($str) - 1;
    $num = 0;
    for ($i = 0; $i < $length; $i++) {
        $num = rand(0, $l);
        $a = $str[$num];
        $result = $result . $a;
    }
    return $result;
}

//T-Team设置cookie
function set_cookie($key, $value = '', $time = 3600)
{
    $appID = config('appID');
	$prefix = empty($appID) ? 'admin' : $appID;
    $time = $time > 0 ? $time : 0;
    return setcookie($prefix.'_'.$key, cp_encode($value, $key), time()+$time);
}

//T-Team获取cookie
function get_cookie($key)
{
    $appID = config('appID');
	$prefix = empty($appID) ? 'admin' : $appID;
    return isset($_COOKIE[$prefix.'_'.$key]) ? cp_decode($_COOKIE[$prefix.'_'.$key], $key) : false;
}

//T-Team设置session
function set_session($key, $value = '')
{
    $appID = config('appID');
	$prefix = empty($appID) ? 'admin' : $appID;
	$src = get_session($key);
	if( is_array($value) && is_array($src) ){
		$_SESSION[$prefix.'_'.$key] = array_merge($src,$value);
	}else{
		$_SESSION[$prefix.'_'.$key] = $value;
	}
}

//T-Team获取session
function get_session($key)
{
    $appID = config('appID');
	$prefix = empty($appID) ? 'admin' : $appID;
	if( isset($_SESSION[$prefix.'_'.$key]) ){
		return $_SESSION[$prefix.'_'.$key];
	}else{
		return false;
	}
}

//T-Team删除字符串里的回车、空格
function delnr($str){
$str = trim($str);
$str = preg_replace('/\n/', '', $str);
$str = preg_replace('/\r/', '', $str);
return $str;
}

//T-Team日志记录
function logger($filename , $log_content)
{
    $max_size = 100000;
    $log_filename = 'cache/userlog/'.$filename.".xml";
    if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
    file_put_contents($log_filename, date('Y-m-d H:i:s')." ".$log_content."\r\n", FILE_APPEND);
}

//T-Team数组排序
function array_order($array, $key, $type = 'asc', $reset = true)
{
    if (empty($array) || !is_array($array)) {
        return $array;
    }
    foreach ($array as $k => $v) {
        $keysvalue[$k] = $v[$key];
    }
    if ($type == 'asc') {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    $i = 0;
    foreach ($keysvalue as $k => $v) {
        $i++;
        if ($reset) {
            $new_array[$i] = $array[$k];
        } else {
            $new_array[$k] = $array[$k];
        }
    }
    return $new_array;
}

/**
 * T-Team根据字段进行排序
 * @params array $array 需要排序的数组
 * @params string $field 排序的字段
 * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
 */
function arraySequence($array, $field, $sort = 'SORT_DESC')
{
    $arrSort = array();
    foreach ($array as $uniqid => $row) {
        foreach ($row as $key => $value) {
            $arrSort[$key][$uniqid] = $value;
        }
    }
    array_multisort($arrSort[$field], constant($sort), $array);
    return $array;
}

//T-Team添加获取系统版本号
function getVer(){
	return file_get_contents('conf/ver.php');
}
	
