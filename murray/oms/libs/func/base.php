<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 基础函数库
*/

defined('INPOP') or exit('Access Denied');

//显示API结果
function apishow($str){
	header("Content-Type:text/html;charset=utf-8");
	echo $str;
	exit;
}

//过滤非法字符
function filterString(){

}

//分割路径
function getClassDir($name){
	if(!$name) return false;
	$pathArray = explode('_', $name);
	//最后一个为类名
	$className = end($pathArray);
	//将最后一个刨除，获取路径
	array_pop($pathArray);
	if(!empty($pathArray)) $return['path'] = implode(DS, $pathArray);
	$return['className'] = $className;
	return $return;
}

//检验目录
function dir_path($dirpath){
	if(substr($dirpath, -1) != DS) $dirpath = $dirpath.DS;
	return $dirpath;
}

//生成目录
function dir_create($path, $mode = 777){
	if(is_dir($path)) return TRUE;
	$dir = str_replace(BASE_PATH.DS, '', $path);
	$dir = dir_path($dir);
    $temp = explode(DS, $dir);
    $cur_dir = BASE_PATH.DS;
	$max = count($temp) - 1;
    for($i=0; $i<$max; $i++){
        $cur_dir .= $temp[$i].DS;
        if(is_dir($cur_dir)) continue;
		mkdir($cur_dir);
		@chmod($cur_dir, 0777);
    }
	return is_dir($path);           
}

//获取表名
function getTable($name){
	global $_config;
	return $_config['db']['tablepre'].$name;
}

//单位转换
function setupSize($fileSize) { 
	$size = sprintf("%u", $fileSize); 
	if($size == 0) return("0 Bytes");
	$sizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB"); 
	return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizename[$i];
}

//下载文件
function downloadFile($filePath, $filename, $charset = 'UTF-8', $mimeType = 'application/octet-stream'){  
    //文件名乱码问题  
    if(preg_match("/MSIE/", $_SERVER["HTTP_USER_AGENT"])) {  
        $filename = urlencode($filename);  
        $filename = str_replace("+", "%20", $filename);// 替换空格  
        $attachmentHeader = "Content-Disposition: attachment; filename=\"{$filename}\"; charset={$charset}";  
    }else if(preg_match("/Firefox/", $_SERVER["HTTP_USER_AGENT"])) {            
        $attachmentHeader = 'Content-Disposition: attachment; filename*="utf8\'\'' . $filename. '"' ;  
    }else{  
        $attachmentHeader = "Content-Disposition: attachment; filename=\"{$filename}\"; charset={$charset}";  
    }
    $filesize = filesize($filePath);  
  
    header("Pragma: public");
	header("Expires: 0");  
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");  
    header("Content-Type: application/force-download");  
    header("Content-Type: ".$mimeType);  
  
    header($attachmentHeader);  
    header('Pragma: cache');  
    header('Cache-Control: public, must-revalidate, max-age=0');  
    header("Content-Length: ".$filesize);  
    readfile($filePath);  
    exit;  
}

//64编码
function doEncode($txt, $key){
	srand((double)microtime() * 1000000);
	$encrypt_key = md5(rand(0, 32000));
	$ctr = 0;
	$tmp = '';
	for($i = 0;$i < strlen($txt); $i++){
		$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		$tmp .= $encrypt_key[$ctr].($txt[$i] ^ $encrypt_key[$ctr++]);
	}
	return base64_encode(mk_key($tmp, $key));
}

//64解码
function doDecode($txt, $key){
	$txt = mk_key(base64_decode($txt), $key);
	$tmp = '';
	for ($i = 0;$i < strlen($txt); $i++){
		$md5 = $txt[$i];
		$tmp .= $txt[++$i] ^ $md5;
	}
	return $tmp;
}

//加密
function mk_key($txt, $encrypt_key){
	$encrypt_key = md5($encrypt_key);
	$ctr = 0;
	$tmp = '';
	for($i = 0; $i < strlen($txt); $i++){
		$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
	}
	return $tmp;
}

function isAuth($txt, $operation = 'ENCODE', $key = ''){
	$key = $key ? $key : $GLOBALS['is_auth_key'];
    return $operation=='ENCODE' ? doEncode($txt, $key) : doDecode($txt, $key);
}

function new_htmlspecialchars($string){
    return is_array($string) ? array_map('new_htmlspecialchars', $string) : htmlspecialchars($string,ENT_QUOTES);
}

function new_addslashes($string){
	if(!is_array($string)) return addslashes($string);
	foreach($string as $key => $val) $string[$key] = new_addslashes($val);
	return $string;
}

function new_stripslashes($string){
	if(!is_array($string)) return stripslashes($string);
	foreach($string as $key => $val) $string[$key] = new_stripslashes($val);
	return $string;
}

function strip_textarea($string){
	return nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($string, ENT_QUOTES)));
}

function strip_js($string, $js = 1){
	$string = str_replace(array("\n","\r","\""),array('','',"\\\""),$string);
	return $js==1 ? "document.write(\"".$string."\");\n" : $string;
}

function str_safe($string){
	$searcharr = array("/(javascript|jscript|js|vbscript|vbs|about):/i","/on(mouse|exit|error|click|dblclick|key|load|unload|change|move|submit|reset|cut|copy|select|start|stop)/i","/<script([^>]*)>/i","/<iframe([^>]*)>/i","/<frame([^>]*)>/i","/<link([^>]*)>/i","/@import/i");
	$replacearr = array("\\1\n:","on\n\\1","&lt;script\\1&gt;","&lt;iframe\\1&gt;","&lt;frame\\1&gt;","&lt;link\\1&gt;","@\nimport");
	$string = preg_replace($searcharr,$replacearr,$string);
	$string = str_replace("&#","&\n#",$string);
	return $string;
}

function random($length, $chars = '0123456789'){
	$hash = '';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++){
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}

function mkCookie($var, $value = '', $time = 0){
	global $_config;
	$time = $time > 0 ? $time : (empty($value) ? time() - 3600 : 0);
	$s = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
	$var = $_config['cookie']['pre'].$var;
	return setcookie($var, $value, $time, $_config['cookie']['path'], $_config['cookie']['domain'], $s);
}

function getCookie($var){
	global $_config;
	$var = $_config['cookie']['pre'].$var;
	return isset($_COOKIE[$var]) ? $_COOKIE[$var] : FALSE;
}

function str_cut($string, $length, $dot = ' ...'){
	global $CONFIG;
	$strlen = strlen($string);
	if($strlen <= $length) return $string;
	$string = str_replace(array('&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;'), array(' ', '&', '"', "'", '“', '”', '—', '<', '>'), $string);
	$strcut = '';
	$n = $tn = $noc = 0;
	while($n < $strlen){
		$t = ord($string[$n]);
		if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
			$tn = 1; $n++; $noc++;
		} elseif(194 <= $t && $t <= 223) {
			$tn = 2; $n += 2; $noc += 2;
		} elseif(224 <= $t && $t < 239) {
			$tn = 3; $n += 3; $noc += 2;
		} elseif(240 <= $t && $t <= 247) {
			$tn = 4; $n += 4; $noc += 2;
		} elseif(248 <= $t && $t <= 251) {
			$tn = 5; $n += 5; $noc += 2;
		} elseif($t == 252 || $t == 253) {
			$tn = 6; $n += 6; $noc += 2;
		} else {
			$n++;
		}
		if($noc >= $length) break;
	}
	if($noc > $length) $n -= $tn;
	$strcut = substr($string, 0, $n);
	$strcut = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&#039;', '&lt;', '&gt;'), $strcut);
	return $strcut.$dot;
}

//清除空元素
function array_remove_empty(&$arr, $trim = true){   
	foreach ($arr as $key => $value) {   
		if (is_array($value)) {   
			array_remove_empty($arr[$key]);   
		} else {   
			$value = trim($value);   
			if ($value == '') {   
				unset($arr[$key]);   
			} elseif ($trim) {   
				$arr[$key] = $value;   
			}   
		}   
	}   
}

//过滤SQL
function strip_sql($string){
    $search_arr = array("/ union /i","/ select /i","/ update /i","/ outfile /i","/ or /i");
    $replace_arr = array('&nbsp;union&nbsp;','&nbsp;select&nbsp;','&nbsp;update&nbsp;','&nbsp;outfile&nbsp;','&nbsp;or&nbsp;');
    return is_array($string) ? array_map('strip_sql', $string) : preg_replace($search_arr, $replace_arr, $string);
}


//处理POST和GET
function do_extract(){
	$magic_quotes_gpc = get_magic_quotes_gpc();
	if(!$magic_quotes_gpc){
		$_POST = new_addslashes($_POST);
		$_GET = new_addslashes($_GET);
	}
	@extract($_POST, EXTR_OVERWRITE);
	@extract($_GET, EXTR_OVERWRITE);
	unset($_POST, $_GET);
}

//附加样式
function style($title, $style = ''){
	return $style == '' ? $title : "<samp style=\"$style\">$title</samp>";
}


function linkurl($linkurl, $isabs = 0){
	global $PHP_SITEURL;
	if(strpos($linkurl, '://') !== FALSE || $linkurl[0] == '?') return $linkurl;
    if($isabs || defined('SHOWJS')){
		return strpos($linkurl, ROOT_PATH) === 0 ? $PHP_SITEURL.substr($linkurl, strlen(ROOT_PATH)) : $PHP_SITEURL.$linkurl;
	}else{
		return strpos($linkurl, ROOT_PATH) === 0 ? $linkurl : ROOT_PATH.$linkurl;
	}
}

function imgurl($imgurl = '', $isabs = 0){
	$imgurl = $imgurl == '' ? 'images/nopic.gif' : $imgurl;
	return linkurl($imgurl, $isabs);
}

//建立分页
function dopages($total, $page = 1, $perpage = 10, $url = ''){   
	global $PHP_URL,$LANG;   
	if(!$url) $url = preg_replace("/(.*)([&?]page=[0-9]*)(.*)/i", "\\1\\3", $PHP_URL);   
	$s = strpos($url, '?') === FALSE ? '?' : '&';   
	$page = $page > 0 ? $page : 1;   
	$pages = ceil($total/$perpage);   
	$page = min($pages,$page);   
	$prepg = $page-1;   
	//$nextpg = $page==$pages ? 0 : ($page+1); 
	$nextpg = $page+1;  
	if($total<1) return false;   
	$pagenav .="";   
	if ($prepg < 1){   
		$pagenav .= "";   
		$pagenav .= "";   
	} else {   
		$pagenav .= "<li><a href='$url{$s}page=1' target='_self'> <<</a></li> ";   
		$pagenav .= "<li><a href='$url{$s}page=1' target='_self'> << </a> </li>";   
	}   
	if ( $page !=1 ) {$pagenav .= "<li><a href='$url{$s}page=1' target='_self'>1</a></li> ";}   
	if ( $page >= 5 ) {$pagenav .= "<span>……</span> ";}   
	if ($pages > $page + 2){$endPage = $page + 2;} else {$endPage = $pages;}   
	for ($i = $page - 2; $i <= $endPage; $i++) {   
		if ($i > 0) {   
			if ($i == $page) {   
				$pagenav .= "<li class='active'><a href='#'>$i</a> </li>";   
			}else{   
				if($i != 1 && $i != $pages){   
				  $pagenav .= "<li><a href='$url{$s}page={$i}' target='_self'>$i</a> </li>";   
				}   
			}   
		}   
	}   
	if ($page + 3 < $pages) $pagenav .="<span>……</span> ";   
	if ($page != $pages) $pagenav .="<li><a href='$url{$s}page={$pages}' target='_self'>$pages</a> </li>";   
	if ($nextpg >= $pages) {   
		$pagenav .= "";   
		$pagenav .= "";   
	} else {   
		$pagenav .= "<li><a href='$url{$s}page={$nextpg}' target='_self'> >> </a></li> ";   
		$pagenav .= "<li><a href='$url{$s}page={$pages}' target='_self'> >> </a></li>";   
	}   
	$pagenav .="";   
	return $pagenav;   
} 

//获取平台
function getOSInfo($useragent = ''){
	// iphone  
	$is_iphone  = strripos($useragent,'iphone');  
	if($is_iphone){  
		return 'iphone';  
	}  
	// android  
	$is_android    = strripos($useragent,'android');  
	if($is_android){  
		return 'android';  
	}  
	// 微信  
	$is_weixin  = strripos($useragent,'micromessenger');  
	if($is_weixin){  
		return 'weixin';  
	}  
	// ipad  
	$is_ipad    = strripos($useragent,'ipad');  
	if($is_ipad){  
		return 'ipad';  
	}  
	// ipod  
	$is_ipod    = strripos($useragent,'ipod');  
	if($is_ipod){  
		return 'ipod';  
	}  
	// pc电脑  
	$is_pc = strripos($useragent,'windows nt');  
	if($is_pc){  
		return 'pc';  
	}  
}
?>