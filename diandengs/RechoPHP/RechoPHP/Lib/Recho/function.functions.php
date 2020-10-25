<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

/**
 * public function
 * $Author: Recho $license: http://www.recho.net/ $
 * $create time: 2012-08-19 01:41
 * $last update time: 2012-08-19 01:41 Recho $
 */
function M( $model){
	return rc::M( $model);
}

/**
 * 返回自定义模型
 * @param unknown_type $model
 */
function D( $model){
	return rc::D( $model);
}

/**
 * 返回数据库表
 * @param $table
 */
function T( $table){
	return 'qc_'.$table;
}

function cache( $mode=false){
	return Cache::run( $mode);
}

// 循环创建目录
function mk_dir($dir, $mode = 0755)
{
	if (is_dir($dir) || @mkdir($dir,$mode)) return true;
	if (!mk_dir(dirname($dir),$mode)) return false;
	return @mkdir($dir,$mode);
}

/**
 * URL组装 支持不同模式
 * @param unknown_type $url			array("模块/操作?参数")
 * @param unknown_type $vars		参数
 * @param unknown_type $suffix		伪静态后缀
 * @param unknown_type $redirect	是否跳转
 * @param unknown_type $domain		显示域名
 * @return unknown
 */
function U( $url, $vars='', $suffix=false, $redirect=false, $domain=true){
	$url = parse_url( $url);
	$vars = is_array($vars) ? http_build_query($vars):$vars;
	$vars = !empty( $url['query']) ? "?{$url['query']}".(empty($vars) ? '':"&$vars"):(empty($vars) ? '':'?'.$vars);
	$suffix = $suffix ? ($suffix=($suffix = $suffix===true?C('URL_HTML_SUFFIX'):$suffix) ? '.'.ltrim($suffix,'.'):''):'';
	$domain = $domain===true ? C('WWWBASEURL/'):$domain;
	switch( C('URL_PATH_MOD')){
		case 1:$url = $url['path'] . $suffix . $vars;;break;
		case 2:if( is_string($vars) && !empty($vars)){parse_str(substr( $vars, 1), $vars);$tVars='';foreach( $vars as $k=>$v) $tVars.="/$k/$v";}$url = $url['path'].$tVars;break;
		case 3:$mods=explode( '/', $url['path']);$url = '?'.C('VAR_DEFAULT_MOD')."={$mods[1]}&".C('VAR_DEFAULT_ACT')."={$mods[2]}" . (empty( $vars) ? '':'&'.substr($vars, 1));break;
		case 4:if( !function_exists('diyU')) exit('出错了，此项目未定义有自定义URL模式函数！');$mods=explode( '/', $url['path']);$vars=empty( $vars) ? '':'&'.substr($vars, 1);return diyU( $mods[1], $mods[2], $vars, $domain, $redirect);break;
	}
	
	if( $redirect) redirect( $domain.$url);
	return $domain.$url;
}

/**
 * 以当前地址为准获取过滤重复值的URL
 * @param unknown_type $vars
 */
function fU( $url=false, $vars=array(), $cancelEmpty=false){
	$url = $url ? $url:functions::requestUri();
	if( empty($vars)) return $url;
	$vars = http_build_query($vars);
	$url = $url . (functions::isHaveQuery() ? "&$vars":"?$vars");
	return functions::filterUrl( $url, $cancelEmpty);
}

/**
 * 获取配置值
 * @param unknown_type $name	键
 * @param unknown_type $value	值
 * @return unknown
 */
function C( $name=null, $value=null){
	static $_config = array();
	if( empty( $name)) return $_config;
    //优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtolower($name);
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : null;
            $_config[$name] = $value;
            return;
        }
        //二维数组设置和获取支持
        $name = explode('.', $name);
        $name[0]   =  strtolower($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
        $_config[$name[0]][$name[1]] = $value;
        return;
    }
    //批量设置
    if (is_array($name)){
        return $_config = array_merge($_config, array_change_key_case($name));
    }
    return null; //避免非法参数
}

/**
 * URL重定向
 * @param unknown_type $url
 * @param unknown_type $time
 * @param unknown_type $msg
 */
function redirect($url, $time=0, $msg='') {
    //多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);
    if (empty($msg))
        $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
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
        $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $msg;
        exit($str);
    }
}

/* xml编码 */
function xml_encode($data,$encoding='utf-8',$root="think") {
	$xml = '<?xml version="1.0" encoding="'.$encoding.'"?>';
	$xml.= '<'.$root.'>';
	$xml.= data_to_xml($data);
	$xml.= '</'.$root.'>';
	return $xml;
}
function data_to_xml($data) {
	if(is_object($data)) {
		$data = get_object_vars($data);
	}
	$xml = '';
	foreach($data as $key=>$val) {
		is_numeric($key) && $key="item id=\"$key\"";
		$xml.="<$key>";
		$xml.=(is_array($val)||is_object($val))?data_to_xml($val):$val;
		list($key,)=explode(' ',$key);
		$xml.="</$key>";
	}
	return $xml;
}

/**
 * 转换为安全的纯文本
 *
 * @param string  $text
 * @param boolean $parse_br    是否转换换行符
 * @param int     $quote_style ENT_NOQUOTES:(默认)不过滤单引号和双引号 ENT_QUOTES:过滤单引号和双引号 ENT_COMPAT:过滤双引号,而不过滤单引号
 * @return string|null string:被转换的字符串 null:参数错误
 */
function secure($text, $parse_br = false, $quote_style = ENT_NOQUOTES){
	if (is_numeric($text))
		$text = (string)$text;

	if (!is_string($text))
		return null;

	if (!$parse_br) {
		$text = str_replace(array("\r","\n","\t"), ' ', $text);
	} else {
		$text = nl2br($text);
	}

	//$text = stripslashes($text);
	$text = htmlspecialchars($text, $quote_style, 'UTF-8');

	return $text;
}

/**
 * 树形变量打印
 * @param unknown_type $var
 */
function dump( $var){
	echo "<pre>";var_dump( $var);exit;
}

/*
 *对搜索字符进行加码
*/
function searchEncode($keyword){
	$keyword = functions::addslashes_deep($keyword);
	$keyword = str_replace(',', '%2c', base64_encode(serialize(trim($keyword))));
	return str_replace('+', '%2b', $keyword);
}
//解密
function searchDecode($keyword){
	$keyword = str_replace('%2b','+',  $keyword);
	$keyword = str_replace('%2c',',',  $keyword);
	return functions::addslashes_deep(unserialize(base64_decode(trim($keyword))));
}