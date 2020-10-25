<?php
//版权所有(C) 2014 www.ilinei.com

//扩展函数
function autoload($class){
    $namespaces = array(
    'ilinei' => '/source/',
    'tpl' => ' '
    );

    $paths = explode('\\', $class);

    $file = '';
    if($namespaces[$paths[0]]) $file = ROOTPATH.trim($namespaces[$paths[0]]).strtr($class, '\\', '/').'.php';
    else $file = ROOTPATH.'/module/'.strtr($class, '\\', '/').'.php';

    if(is_file($file)) include_once $file;
    else throw new Exception($GLOBALS['lang']['error.file'].$file.' '.$class);
}

function str_encrypt($str, $crypt = ''){
    global $config;

    $scrypt = $crypt ? $crypt : $config['crypt'];
    $str = strrev(substr($scrypt.'-'.crypt($str), 0, 2).base64_encode($str).substr($scrypt.'-'.crypt($str), 0, 4));
    return base64_encode(substr($str, 2));
}

function str_decrypt($str){
    $str = base64_decode($str);
    $str = substr($str, 2);
    $str = substr($str, 0, -2);

    return base64_decode(strrev($str));
}

function check_unix(){
	$osname = strtolower(PHP_OS);
	$unixs = array('linux', 'solaris', 'unix', 'aix');
	return in_array($osname, $unixs);
}

function check_robot($user_agent = '') {
	static $kw_spiders = 'Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla';
	static $kw_browsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';
	
	$user_agent = empty($user_agent) ? $_SERVER['HTTP_USER_AGENT'] : $user_agent;
	
	if(!strexists($user_agent, 'http://') && preg_match("/($kw_browsers)/i", $user_agent)) return false;
	elseif(preg_match("/($kw_spiders)/i", $user_agent)) return true;
	else return false;
}

function check_weixin(){
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) return true; 
	return false;
}

function debug($val, $exit = 0){
	echo('<pre>');
	
	if(is_array($val) || is_object($val) || is_resource($val)) print_r($val);
	else var_dump($val);
	
	echo('</pre>');
	
	if($exit) exit();
}

function eaddslashes($string, $force = 0, $strip = false) {
	if(!MAGICQUOTESGPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = eaddslashes($val, $force, $strip);
			}
		} else $string = addslashes($strip ? stripslashes($string) : $string);
	}
	
	return $string;
}

function eimplode($array) {
	if(!empty($array)) return "'".implode("','", is_array($array) ? $array : array($array))."'";
	else return 0;
}

function get_client_ip() {
	$ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
		foreach ($matches[0] AS $xip) {
			if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
				$ip = $xip;
				break;
			}
		}
	}
	return $ip;
}

function get_microtime() {
	return sprintf('%.04f', array_sum(explode(' ', microtime())));
}

function get_slashe(){
	return strpos($_SERVER['HTTP_USER_AGENT'], 'Windows') !== false ? "\\" : "/";
}

function get_uuid(){
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
	mt_rand(0, 0x0fff) | 0x4000,
	mt_rand(0, 0x3fff) | 0x8000,
	mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
}

function get_rand($rand_arr){ 
    $result = ''; 
    $rand_sum = array_sum($rand_arr); 
    
    foreach($rand_arr as $key => $num) { 
        $rand_num = mt_rand(1, $rand_sum); 
        if ($rand_num <= $num){ 
            $result = $key; 
            break; 
        }else $rand_sum -= $num; 
        
        unset($rand_num); 
    }
    
    return $result; 
}

function get_file_ext($filename) {
	return addslashes(strtolower(substr(strrchr($filename, '.'), 1, 10)));
}

function get_file_stat($filename){
	$stat = stat($filename);
	
	return array(
	'size' => $stat['size'], 
	'atime' => $stat['atime'], 
	'mtime' => $stat['mtime'], 
	'ctime' => $stat['ctime']
	);
}

function get_file_lines($filename, $line = 1){
	$lines = array();
	
	$handle = fopen($filename, 'r');
    if($handle){
	    while(!feof($handle)){
	    	if(count($lines) >= $line) break;
	        $lines[] = fgets($handle);
	   }
    }
    
    fclose($handle);
    
    return $lines;
}

function array2xml($arr, $root = 'xml'){
	if(!is_array($arr)) return '';
	
	$xml = "<{$root}>\r\n";
	foreach($arr as $key => $val){
		if(!is_array($val)) $xml .= "\t<{$key}>{$val}</{$key}>\r\n";
		else{
			$xml .= "\t<{$key}>";
			foreach($val as $ckey => $cval) $xml .= "\t\t<{$ckey}>{$cval}</{$ckey}>\r\n";
			
			$xml .= "\t</{$key}>\r\n";
		}
	}
	
	$xml .= "</{$root}>";
	
	return $xml;
}

function dnull($obj, $default = '', $except = ''){
	return $obj === null || !isset($obj) || $obj == $except ? $default : $obj;
}

function dempty($obj, $default = ''){
	return empty($obj) ? $default : '';
}

function is_ansi($str){
	return preg_match("/^[\w\_]+$/", $str) > 0 ? true : false;
}

function is_email($str){
	return preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/", $str) > 0 ? true : false;
}

function is_gb($str){
	return preg_match("/^[\u4e00-\u9fa5]+$/", $str) > 0 ? true : false;
}

function is_cint($str){
	return preg_match("/^[+-]?[0-9]+$/", $str) > 0 ? true : false;
}

function is_address($str){
	return preg_match("/^\d+\.\d+\.\d+\.\d+$/", $str) > 0 ? true : false;
}

function is_cnumber($str){
	return preg_match("/^-?\d+(\.\d+)?$/", $str) > 0 ? true : false;
}

function is_rgb($str){
	return preg_match("/^#[0-9a-fA-F]{6}$/", $str) > 0 ? true : false;
}

function is_mobile($str){
	return preg_match("/^1(3|5|7|8)\d{8,9}$/", $str) > 0 ? true : false;
}

function is_phone($str){
	return preg_match("/^\d{3}-\d{8}|\d{4}-\d{7}$/", $str) > 0 ? true : false;
}

function is_shortdate($str){
	return preg_match("/^\d{4}-\d{1,2}-\d{1,2}$/", $str) > 0 ? true : false;
}

function is_datetime($str){
	return preg_match("/^\d{4}-\d{1,2}-\d{1,2}\s\d{2}:\d{2}$/", $str) > 0 ? true : false;
}

function is_timestamp($str){
	return preg_match("/^\d{4}-\d{1,2}-\d{1,2}\s\d{2}:\d{2}:\d{2}$/", $str) > 0 ? true : false;
}

function is_uuid($str){
	return preg_match("/^[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}$/", $str) > 0 ? true : false;
}

function is_image($ext){
	return in_array(strtolower($ext), array('jpg', 'jpeg', 'gif', 'bmp', 'png'));
}

function strexists($string, $find) {
	return !(strpos($string, $find) === false);
}

function substring($str, $start = 0, $end = -1){
	if($end == -1) return substr($str, $start);
	$len = $end - $start;
	return $len > 0 ? substr($str, $start, $len) : substr($str, $end, - $len);
}

function cutstr($string, $length, $dot = ' ...') {
    if(strlen($string) <= $length) return $string;

    $pre = chr(1);
    $end = chr(1);
    $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

    $strcut = '';
    $n = $tn = $noc = 0;
    while($n < strlen($string)) {

        $t = ord($string[$n]);
        if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
            $tn = 1; $n++; $noc++;
        } elseif(194 <= $t && $t <= 223) {
            $tn = 2; $n += 2; $noc += 2;
        } elseif(224 <= $t && $t <= 239) {
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

        if($noc >= $length) {
            break;
        }
    }

    if($noc > $length) $n -= $tn;

    $strcut = substr($string, 0, $n);
    $strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

    $pos = strrpos($strcut, chr(1));
    if($pos !== false) $strcut = substr($strcut,0,$pos);

    return $strcut.$dot;
}

function utf8substr($str, $start, $length){
	if(function_exists('mb_substr')) return mb_substr($str, $start, $length, 'utf-8');
	else{
		$len = strlen($str);
		if($len < $start + 1) return '';
		if($len < $start + $length || $length == 0) return $str;
		if($start < 0) $start = $len + $start;
		if($start < 0) $start = 0;
		if($length < 0) $length = $len + $length;
		if($length < 0) $length = $len;
		if($start > $length) $start = $length;
		
		$uchars = 0;
		for($i = 0; $i < $len; $i++){
			if($i - $uchars == $start) break;
			if(ord($str{$i}) > 127) $uchars++;
		}
		
		$start = $start + $uchars;
		
		$uchars = 0;
		for($i = $start; $i < $len; $i++){
			if(ord($str{$i}) > 127) $uchars++;
			if($i - $uchars == $length) break;
		}
		
		$length = $length + $uchars;
		
		return substr($str, $start, $length);
	}
}

function strip2words($str, $nr = true){
	$tmp = strip_tags($str);
	$tmp = preg_replace("/\s(?=\s)/s", '', $tmp);
	if($nr) $tmp = preg_replace("/([\n\r]+)/s", '', $tmp);
	
	if(empty($tmp)){
		$tmp = preg_replace("/<[^>]*>/is", '', $str);
		$tmp = preg_replace("/\s(?=\s)/s", '', $tmp);
		if($nr) $tmp = preg_replace("/([\n\r]+)/s", '', $tmp);
	}
	
	$tmp = str_replace('&nbsp;', '', $tmp);
	$tmp = str_replace('  ', ' ', $tmp);
	$tmp = str_replace('　　', ' ', $tmp);
	$tmp = str_replace('	', ' ', $tmp);
	
	return $tmp;
}

function subtimer($timer) {
	$subtimer = time() - $timer;
	
	if($subtimer > 3600 * 216) {
		$timestring =  date('m-d', $timer);
		return $timestring;
	}elseif($subtimer > 3600 * 24 && $subtimer < 3600 * 216) {
		return intval($subtimer / (3600 * 24)).'天前';
	}elseif($subtimer > 3600 * 12) {
		return '半天前';
	}elseif($subtimer > 3600) {
		return intval($subtimer / 3600).'小时前';
	} elseif($subtimer > 1800) {
		return '半小时前';
	} elseif($subtimer > 60) {
		return intval($subtimer / 60).'分钟前';
	} elseif($subtimer > 0) {
		return $subtimer.'秒前';
	} elseif($subtimer == 0) {
		return '刚刚';
	} else {
		return '';
	}
}

function timespan($timer1, $timer2){
	$timespan = $timer1 - $timer2;
	
	return (int)($timespan / 3600 / 24);
}

function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}

function referer() {
	$_referer = $_SERVER['HTTP_REFERER'];
	$_referer = substr($_referer, -1) == '?' ? substr($_referer, 0, -1) : $_referer;
	$_referer = strrpos($_referer, '?') !== false ? substr($_referer, strrpos($_referer, '?') ) : $_referer;
	
	$_referer = htmlspecialchars($_referer);
	$_referer = str_replace('&amp;', '&', $_referer);
	return strip_tags($_referer);
}

function exit_json($message){
	echo(json_encode($message));
	exit(0);
}

function exit_json_message($message, $success = false, $userid = 0){
	exit_json(array('userid' => $userid, 'success' => $success, 'message' => $message));
}

function exit_echo($message){
	echo($message);
	exit(0);
}

function exit_html5($message){
	echo("
<!DOCTYPE html>
<html>
<head>
<meta charset=\"utf-8\"/>
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0\"/>
</head>
<body>
	<p style=\"padding-top:2rem; text-align:center;\">{$message}</p>
</body>
</html>
");
	exit(0);
}

function show_exception($exception){
	global $setting;
	
	if(defined('ADMIN_SCRIPT') || defined('DEBUG') || $setting['SiteLogDatabase']){
		$__tpl = new \ilinei\template('/module/admin/view/show_exception');
		include_once $__tpl->parsed();
	}
	
	exit_html5('Access Denied.');
}

function show_message($message, $url_forward = '', $template = '/module/admin/view/show_message'){
    global $_var;

	$url_forward = str_replace('{ADMIN_SCRIPT}', ADMIN_SCRIPT,  $url_forward);
	
	$page_title = $GLOBALS['lang']['show_message.title'];
	$refresh_time = 3 * 1000;
	$show_message = $message;
	
	if($url_forward) $extra = 'setTimeout("window.location.href =\''.$url_forward.'\';", '.$refresh_time.');';
	elseif($url_forward !== 0) $extra = 'setTimeout("history.go(-1);", '.$refresh_time.');';
	
	$show_message .= $extra ? '<script type="text/javascript" reload="1">'.$extra.'</script>' : '';
	
	$__tpl = new \ilinei\template($template);
	include_once $__tpl->parsed();
	
	exit(0);
}

function tshow_message($message, $url_forward = '', $template = 'show_message') {
	global $setting;
	
	$THEME = $setting['SiteTheme'].'/{$THEME}';
	
	$refresh_time = 3 * 1000;
	
	$show_message = $message;
	
	if($url_forward) $extra = 'setTimeout("window.location.href =\''.$url_forward.'\';", '.$refresh_time.');';
	elseif($url_forward !== 0) $extra = 'setTimeout("history.go(-1);", '.$refresh_time.');';
	
	$show_message .= $extra ? '<script type="text/javascript" reload="1">'.$extra.'</script>' : '';
	
	$page_title = $GLOBALS['lang']['show_message.title'];
	
	$__tpl = new \ilinei\template('/'.$setting['SiteTheme'].'/'.$template);
	include_once $__tpl->parsed();
	
	exit(0);
}

function fshow_message($callback, $jsonmessage) {
	$tempHTML = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
		<meta http-equiv="X-UA-Compatible" content="IE=9" />
		<title>iframe message</title>
	</head>
	<body>
	<script type="text/javascript">';
	
	if($callback) $tempHTML .= "parent.{$callback}({$jsonmessage});";
	
	$tempHTML .= '
	</script>
	</body>
</html>';
	
	echo($tempHTML);
	exit();
}

function pager($num, $perpage, $curpage, $mpurl, $psize = 10, $show_psizes = true, $simple = false){
	global $_var, $ADMIN_SCRIPT;
	
	$mpurl = str_replace('{ADMIN_SCRIPT}', $ADMIN_SCRIPT,  $mpurl);
	
	$page = 4;
	
	$a_name = '';
	if(strpos($mpurl, '#') !== false) {
		$a_strs = explode('#', $mpurl);
		$mpurl = $a_strs[0];
		$a_name = '#'.$a_strs[1];
	}
	
	$GLOBALS['lang']['prev'] = $GLOBALS['lang']['multi.prev'];
	$GLOBALS['lang']['next'] = $GLOBALS['lang']['multi.next'];

	$multipage = '';
	$mpurl .= strpos($mpurl, '?') !== false ? '&amp;' : '?';

	$realpages = 1;
	$_var['page_next'] = 0;
	$page -= strlen($curpage) - 1;
	if($page <= 0)  $page = 1;
	
	$offset = floor($page * 0.5);
	$realpages = @ceil($num / $perpage);
	$pages = $realpages;

	if($page > $pages) {
		$from = 1;
		$to = $pages;
	} else {
		$from = $curpage - $offset;
		$to = $from + $page - 1;
		if($from < 1) {
			$to = $curpage + 1 - $from;
			$from = 1;
			if($to - $from < $page)  $to = $page;
		} elseif($to > $pages) {
			$from = $pages - $page + 1;
			$to = $pages;
		}
	}
	
	$_var['page_next'] = $to;

	$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'psize='.$psize.'&page=1'.$a_name.'" class="first">1 ...</a>' : '').
	($curpage > 1 ? '<a href="'.$mpurl.'psize='.$psize.'&page='.($curpage - 1).$a_name.'" class="prev">'.$GLOBALS['lang']['prev'].'</a>' : '');
	for($i = $from; $i <= $to; $i++) {
		$multipage .= $i == $curpage ? '<span><strong>'.$i.'</strong></span>' :
		'<a href="'.$mpurl.'psize='.$psize.'&page='.$i.'">'.$i.'</a>';
	}

	$multipage .= ($to < $pages ? '<a href="'.$mpurl.'psize='.$psize.'&page='.$pages.$a_name.'" class="last">... '.$realpages.'</a>' : '').
	($curpage < $pages ? '<a href="'.$mpurl.'psize='.$psize.'&page='.($curpage + 1).$a_name.'" class="next">'.$GLOBALS['lang']['next'].'</a>' : '');
	
	$psizes = array(10 => '', 20 => '', 30 => '', 40 => '', 50 => '', 100 => '', 500 => '');
	$psizes[$psize] = ' class="selected"';
	
	if($num > $perpage) $multipage = '<span class="pages">'.$multipage.'</span>';
	
	if($show_psizes && $realpages > 1){
		$multipage .= '<span class="psizes">';
		$multipage .= '<a href="'.$mpurl.'psize=10"'.$psizes[10].'>10</a>';
		$multipage .= '<a href="'.$mpurl.'psize=20"'.$psizes[20].'>20</a>';
		$multipage .= '<a href="'.$mpurl.'psize=30"'.$psizes[30].'>30</a>';
		$multipage .= '<a href="'.$mpurl.'psize=50"'.$psizes[50].'>50</a>';
		$multipage .= '<a href="'.$mpurl.'psize=100"'.$psizes[100].'>100</a>';
		$multipage .= '<a href="'.$mpurl.'psize=500"'.$psizes[500].'>500</a>';
		$multipage .= '</span>';
	}
	
	$multipage = $multipage ? '<div class="pg">'.(!$simple ? '<em>&nbsp;'.$GLOBALS['lang']['multi.total'].$num.$GLOBALS['lang']['multi.record'].'&nbsp;</em>' : '').$multipage.'</div>' : '';
	
	$maxpage = $realpages;
	return $multipage;
}

function rpager($num, $perpage, $curpage, $mpurl, $psize = 10, $ext = '') {
	global $_var;
	
	$page = 6;
	
	$GLOBALS['lang']['prev'] = $GLOBALS['lang']['multi.prev'];
	$GLOBALS['lang']['next'] = $GLOBALS['lang']['multi.next'];

	$multipage = '';

	$realpages = 1;
	$_var['page_next'] = 0;
	$page -= strlen($curpage) - 1;
	if($page <= 0)  $page = 1;
	if($num > $perpage) {
		$offset = floor($page * 0.5);
		$realpages = @ceil($num / $perpage);
		$pages = $realpages;

		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page)  $to = $page;
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}
		
		$_var['page_next'] = $to;
		
		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'_p1.html'.$ext.'" class="first">1 ...</a>' : '').
		($curpage > 1 ? '<a href="'.$mpurl.'_p'.($curpage - 1).'.html'.$ext.'" class="prev">'.$GLOBALS['lang']['prev'].'</a>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<span><strong>'.$i.'</strong></span>' :
			'<a href="'.$mpurl.'_p'.$i.'.html'.$ext.'">'.$i.'</a>';
		}

		$multipage .= ($to < $pages ? '<a href="'.$mpurl.'_p'.$pages.'.html'.$ext.'" class="last">... '.$realpages.'</a>' : '').
		($curpage < $pages ? '<a href="'.$mpurl.'_p'.($curpage + 1).'.html'.$ext.'" class="nxt">'.$GLOBALS['lang']['next'].'</a>' : '');

		$multipage = $multipage ? '<div class="pg">'.'<em>&nbsp;'.$GLOBALS['lang']['multi.total'].$num.$GLOBALS['lang']['multi.record'].'&nbsp;</em>'.$multipage.'</div>' : '';
	}
	
	$maxpage = $realpages;
	
	return $multipage;
}

function view($file){
	global $setting;
	
	$template = new \ilinei\template($file);
	return $template->parsed($setting['SiteTemplateCache'] + 0);
}

//拼音、繁简
function pinyin($str){
	 $ord = ord($str{0});
	 if($ord>=ord('A') && $ord <= ord('z')) return strtoupper($str{0});
	 $string = iconv("UTF-8","gb2312", $str);
	 $asc = ord($string{0}) * 256 + ord($string{1}) - 65536;
	 if($asc>=-20319 and $asc<=-20284)return "A";
	 else if($asc>=-20283 and $asc<=-19776)return "B";
	 else if($asc>=-19775 and $asc<=-19219)return "C";
	 else if($asc>=-19218 and $asc<=-18711)return "D";
	 else if($asc>=-18710 and $asc<=-18527)return "E";
	 else if($asc>=-18526 and $asc<=-18240)return "F";
	 else if($asc>=-18239 and $asc<=-17923)return "G";
	 else if($asc>=-17922 and $asc<=-17418)return "I";
	 else if($asc>=-17417 and $asc<=-16475)return "J";
	 else if($asc>=-16474 and $asc<=-16213)return "K";
	 else if($asc>=-16212 and $asc<=-15641)return "L";
	 else if($asc>=-15640 and $asc<=-15166)return "M";
	 else if($asc>=-15165 and $asc<=-14923)return "N";
	 else if($asc>=-14922 and $asc<=-14915)return "O";
	 else if($asc>=-14914 and $asc<=-14631)return "P";
	 else if($asc>=-14630 and $asc<=-14150)return "Q";
	 else if($asc>=-14149 and $asc<=-14091)return "R";
	 else if($asc>=-14090 and $asc<=-13319)return "S";
	 else if($asc>=-13318 and $asc<=-12839)return "T";
	 else if($asc>=-12838 and $asc<=-12557)return "W";
	 else if($asc>=-12556 and $asc<=-11848)return "X";
	 else if($asc>=-11847 and $asc<=-11056)return "Y";
	 else if($asc>=-11055 and $asc<=-10247)return "Z";
	 else return '_';
}

function zh2cn($str){
	global $_CNCHARS, $_ZHCHARS;
	
	$trans = ''; 
	$count = 0;
	$len = strlen($str);
	
	while($count < $len){
		if(ord($str{$count}) >= 224 && ord($str{$count}) <= 239){
			if(($temp = strpos($_ZHCHARS, $str{$count}.$str{$count + 1}.$str{$count + 2})) !== false){
				$trans .= $_CNCHARS{$temp}.$_CNCHARS{$temp + 1}.$_CNCHARS{$temp + 2};
				$count += 3;
				continue;
			}
		}
		
	    $trans .= $str{$count};
	    $count += 1;
	}
	
	return $trans;
}

function cn2zh($str){   
	global $_CNCHARS, $_ZHCHARS;
	
	$trans = ''; 
	$count = 0;
	$len = strlen($str);
	
	while($count < $len){
		if(ord($str{$count}) >= 224 && ord($str{$count}) <= 239){
			if(($temp = strpos($_CNCHARS, $str{$count}.$str{$count + 1}.$str{$count + 2})) !== false){
				$trans .= $_ZHCHARS{$temp}.$_ZHCHARS{$temp + 1}.$_ZHCHARS{$temp + 2};
				$count += 3;
				continue;
			}
		}
		
	    $trans .= $str{$count};
	    $count += 1;
	}
	
	return $trans;
}

//业务函数
function dmkdir($dir, $mode = 0700, $makeindex = TRUE){
	if(!is_dir($dir)) {
		dmkdir(dirname($dir));
		@mkdir($dir, $mode);
	}
	return true;
}

function log_debug($message){
	$message = "\r\n".date('Y-m-d H:i:s ').'('.get_microtime().')'.$message;
	
	if(!is_dir(ROOTPATH.'/_cache/debug')){
		@mkdir(ROOTPATH.'/_cache/debug', 0755, true);
		@chown(ROOTPATH.'/_cache/debug', 'apache');
	}
	
	$fp = fopen(ROOTPATH.'/_cache/debug/log_'.date('YmdH').'.txt', 'a+');
	fwrite($fp, $message);
	fclose($fp);
}

function log_view($id = ''){
	global $_var;
	
	$message = date('Y-m-d H:i:s ').'|'.$_var['clientip'].'|'.$_var['auth'].'|'.$_SERVER['REQUEST_URI'].'|'.$_SERVER['HTTP_USER_AGENT']."\r\n";
	$file = ($id ? $id.'/' : '').date('YmdH');
	
	$fp = fopen(ROOTPATH."/_cache/viewlog/{$file}", 'a+');
	fwrite($fp, $message);
	fclose($fp);
}

function cache_read($cache_name){
	global $config;
	
	$file_name = $config['host'] ? $config['host'].'.'.$cache_name : $cache_name;
	
	if(!is_file(ROOTPATH."/_cache/cache.{$file_name}.php")) return '';
	
	include_once ROOTPATH."/_cache/cache.{$file_name}.php";
	
	return ${$cache_name};
}

function cache_write($cache_name, $cache_data){
	global $config;
	
	$file_name = $config['host'] ? $config['host'].'.'.$cache_name : $cache_name;
	
	if(is_array($cache_data)){
		$cache_content = "<?php
//版权所有(C) 2014 www.ilinei.com
if(!defined('INIT')) exit('Access Denied');
\$$cache_name = array(\n";
		foreach ($cache_data as $key => $val) {
			if(!is_array($val)) $cache_content .= "'{$key}' => '".str_replace("'", "\'", $val)."',\n";
			else{
				$cache_content .= "\t'$key' => array(\n";
				foreach ($val as $ckey => $cval) {
					if(!is_array($cval)) $cache_content .= "\t\t'{$ckey}' => '".str_replace("'", "\'", $cval)."',\n";
					else{
						$cache_content .= "\t\t'$ckey' => array(\n";
						foreach ($cval as $cckey => $ccval) {
							if(!is_array($ccval)) $cache_content .= "\t\t'{$cckey}' => '".str_replace("'", "\'", $ccval)."',\n";
							else{
								$cache_content .= "\t\t'$cckey' => array(\n";
								foreach ($ccval as $ccckey => $cccval) $cache_content .= "\t\t'{$ccckey}' => '".str_replace("'", "\'", $cccval)."',\n";
								$cache_content .= "\t\t),\n";
							}
							
						}
						$cache_content .= "\t\t),\n";
					}
				}
				$cache_content .= "\t),\n";
			}
		}
		
		$cache_content .= ");?>";
		
		file_put_contents(ROOTPATH."/_cache/cache.{$file_name}.php", $cache_content);
	}else{
		file_put_contents(ROOTPATH."/_cache/cache.{$file_name}.php", $cache_data);
	}
}

function cache_delete($cache_name){
	global $config;
	
	$file_name = $config['host'] ? $config['host'].'.'.$cache_name : $cache_name;
	
	unlink(ROOTPATH."/_cache/cache.{$file_name}.php");
}

function cookie_get($key){
	global $config, $_var;
	
	return $_var['cookie'][$config['cookie']['cookiepre'].$key];
}

function cookie_set($key, $value, $expire){
	global $config;
	
	$key = $config['cookie']['cookiepre'].$key;
	
	setcookie($key, $value, $expire, $config['cookie']['cookiepath'], $config['cookie']['cookiedomain']);
}

function sendmail($to, $subject, $message, $from = '') {
	global $config, $setting;
	
	$smtp = new ilinei\smtp($setting['SmtpHost'], $setting['SmtpPort'], true, $setting['SmtpUser'], $setting['SmtpPassword'], $from ? $from : $setting['SmtpUser']);
	
	$subject = '=?utf-8?B?'.base64_encode($subject).'?=';
	
	return $smtp->sendmail($to, $from, $subject, $message, 'HTML');
}

function thumb_image($cimage, $file, $options = null){
	global $setting;
	
	if($options == null || $options['ImageWidth'] + 0 <= 0) $thumbWidth = $setting['ImageWidth'] + 0 > 80 ? $setting['ImageWidth'] + 0 : 80;
	else $thumbWidth = $options['ImageWidth'] + 0;
	
	if($options == null || $options['ImageHeight'] + 0 <= 0) $thumbHeight = $setting['ImageHeight'] + 0 > 60 ? $setting['ImageHeight'] + 0 : 60;
	else $thumbHeight = $options['ImageHeight'] + 0;
	
	$fileext = strtolower(get_file_ext($file));
	
	if($options == null || $options['ThumbType'] + 0 > 0) $thumbType =  $options['ThumbType'] + 0;
	else $thumbType = 2;
	
	if(in_array($fileext, array('jpg', 'jpeg', 'gif', 'bmp', 'png'))) return $cimage->thumb(ROOTPATH.'/attachment/'.$file, ROOTPATH.'/attachment/'.$file.'.t.'.$fileext, $thumbWidth, $thumbHeight, $thumbType) + 0;
	else return 0;
}

function watermark_image($cimage, $file){
	global $setting;
	
	$fileext = strtolower(get_file_ext($file));
	
	if(in_array($fileext, array('jpg', 'jpeg', 'gif', 'bmp', 'png'))){
		$filename = substr($file, 0, -4);
		$status = $cimage->watermark(ROOTPATH.'/attachment/'.$file, ROOTPATH.'/attachment/'.$filename.'.w.'.$fileext);
		return $status ? $filename.'.w.'.$fileext : $file;
	}else return $file;
}

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

function format_bytes($size) {
	$units = array(' B', ' KB', ' MB', ' GB', ' TB');
	for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
	return round($size, 2).$units[$i];
} 

function format_price($price){
	if(substring($price, 0, -3) == '.00') return $price + 0;
	elseif(substring($price, 0, -2) == '.0') return $price + 0;
	else return $price + 0;
}

function format_discount($discount){
	$discount = $discount + 0;
	if(substr($discount, -1) == '0') return substr($discount, 0, -1);
	elseif(substr($discount, 0, 1) == '0') return $discount;
	else return substr($discount, 0, -1).'.'.substr($discount, -1);
}

function format_file_path($path, $thumb = 0){
	global $config;
	
	$ext = get_file_ext($path);
	
	if($thumb == 1 && strexists($path, "{$ext}.t.{$ext}")) $thumb = 0;
	
	$path = $path.($thumb == 0 ? '' : ".t.{$ext}");
	if(strexists($path, 'http://')) return $path;
	else {
		if(is_image($ext) && $config['image']) return $config['image'].'attachment/'.$path;
		elseif(is_image($ext) && $config['server']['image']) return $config['server']['image'].'attachment/'.$path;
		else return 'attachment/'.$path;
	}
}

function format_row_files($row, $index = 0){
	for($i = 1; $i <= 20; $i++){
		if($row['FILE'.sprintf('%02d', $i)]){
			$tempsrc = $row['FILE'.sprintf('%02d', $i)];
			$row['FILE'.sprintf('%02d', $i)] = explode('|', $row['FILE'.sprintf('%02d', $i)]);
			$row['FILE'.sprintf('%02d', $i)][6] = $row['FILE'.sprintf('%02d', $i)][4];
			$row['FILE'.sprintf('%02d', $i)][5] = $row['FILE'.sprintf('%02d', $i)][3];
			$row['FILE'.sprintf('%02d', $i)][4] = $tempsrc;
			$row['FILE'.sprintf('%02d', $i)][3] = format_file_path($row['FILE'.sprintf('%02d', $i)][0]);
			$row['FILE'.sprintf('%02d', $i)][0] = format_file_path($row['FILE'.sprintf('%02d', $i)][0], $row['FILE'.sprintf('%02d', $i)][2]);
			
			if($index == 1 && $row['FILE'.sprintf('%02d', $i)][2] == 0) $row['FILEINDEX'] = $row['FILE'.sprintf('%02d', $i)];
		}
		
		unset($tempsrc);
		unset($tempimgsize);
	}
	
	return $row;
}

function format_row_file($row, $column){
	if($row[$column]){
		$tempsrc = $row[$column];
		$row[$column] = explode('|', $row[$column]);
		$row[$column][4] = $tempsrc;
		$row[$column][3] = format_file_path($row[$column][0]);
		$row[$column][0] = format_file_path($row[$column][0], $row[$column][2]);
	}
	
	return $row;
}

function format_row_path_parse($matches){
	$path = strtolower($matches);
	
	if(substr($path, 0, 7) != 'http://'){
		if($path[0] != '/') $path = '/'.$path;
		$path = strpos($path, '/attachment/') !== false ? substr($path, strpos($path, '/attachment/') + 1) : $path;
	}
	
	return $path;
}

function format_row_flv_parse($matches){
	$flvpath = format_row_path_parse($matches[1]);
	$flvpath = strexists($flvpath, 'http://') ? $flvpath : "../{$flvpath}";
	
	$prototype_html = '';
	
	$prototypes = explode(' ', $matches[0]);
	foreach($prototypes as $key => $val){
		if(strexists($val, 'width=')) $prototype_html .= ' '.$val;
		elseif(strexists($val, 'height=')) $prototype_html .= ' '.$val;
	}
	
	$flv_html = '';
	$flv_html .= '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player"';
	$flv_html .= $prototype_html;
	$flv_html .= '>';
	$flv_html .= '<param name="movie" value="static/flvplayer.swf">';
	$flv_html .= '<param name="wmode" value="transparent">';
	$flv_html .= '<param name="allowfullscreen" value="true">';
	$flv_html .= '<param name="allowscriptaccess" value="always">';
	$flv_html .= '<param name="flashvars" value="vcastr_file='.$flvpath.'&ampBufferTime=1&amp;{FLVPREVIEW}">';
	$flv_html .= '<embed type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true"';
	$flv_html .= $prototype_html.' ';
	$flv_html .= 'src="static/flvplayer.swf" ';
	$flv_html .= 'flashvars="vcastr_file='.$flvpath.'&ampBufferTime=1&amp;{FLVPREVIEW}">';
	$flv_html .= '</object>';
	
	return $flv_html;
}

function format_row_flv($row, $column){
	$pattern = "/<[embed |EMBED].*?src=[\'|\"](.*?(?:[\.flv|\.mp4]))[\'|\"].*?[\/]?>/";
	
	$row[$column] = preg_replace_callback($pattern, "format_row_flv_parse", $row[$column]);
	if(is_array($row['FILE01'])) $row[$column] = str_replace('{FLVPREVIEW}', 'image='.$row['FILE01'][3], $row[$column]);
	
	return $row;
}

function format_row_mp4_parse($matches){
	$mp4path = format_row_path_parse($matches[1]);
	
	$prototype_html = '';
	
	$prototypes = explode(' ', $matches[0]);
	foreach($prototypes as $key => $val){
		if(strexists($val, 'width=')) $prototype_html .= ' '.$val;
		elseif(strexists($val, 'height=')) $prototype_html .= ' '.$val;
	}
	
	$mp4_html = '';
	$mp4_html .= '<video class="pure-u-1" preload="auto" controls="controls">';
	$mp4_html .= '<source src="'.$mp4path.'" type="video/mp4" />';
	$mp4_html .= '</video>';
	
	return $mp4_html;
}

function format_row_mp4($row, $column){
	$pattern = "/<[embed |EMBED].*?src=[\'|\"](.*?(?:[\.mp4]))[\'|\"].*?[\/]?>/";
	
	$row[$column] = preg_replace_callback($pattern, "format_row_mp4_parse", $row[$column]);
	
	return $row;
}

function format_row_mp3_pc_parse($matches){
	$mp3path = format_row_path_parse($matches[1]);
	
	$prototype_html = '';
	
	$autostart = '';
	
	$prototypes = explode(' ', $matches[0]);
	foreach($prototypes as $key => $val){
		if(strexists($val, 'width=')) $prototype_html .= ' '.$val;
		elseif(strexists($val, 'height=')) $prototype_html .= ' '.$val;
		elseif(strexists($val, 'autostart=')) $autostart = $val;
	}
	
	$autostart = strexists($autostart, '"yes"') ? '&amp;Autoplay=1' : '&amp;Autoplay=0';
	
	$mp3_html = '';
	$mp3_html .= '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player"';
	$mp3_html .= $prototype_html;
	$mp3_html .= '>';
	$mp3_html .= '<param name="movie" value="static/mp3player.swf?url='.$mp3path.$autostart.'">';
	$mp3_html .= '<param name="wmode" value="transparent">';
	$mp3_html .= '<embed type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true"';
	$mp3_html .= $prototype_html.' ';
	$mp3_html .= 'src="static/mp3player.swf?url='.$mp3path.$autostart.'" wmode="transparent"/>';
	$mp3_html .= '</object>';
	
	return $mp3_html;
}

function format_row_mp3_h5_parse($matches){
	$mp3path = format_row_path_parse($matches[1]);
	
	$prototype_html = '';
	
	$autoplay = '';
	$prototypes = explode(' ', $matches[0]);
	foreach($prototypes as $key => $val){
		if(strexists($val, 'width=')) $prototype_html .= ' '.$val;
		elseif(strexists($val, 'height=')) $prototype_html .= ' '.$val;
		elseif(strexists($val, 'autostart=')) $autoplay = $val;
	}
	
	$autoplay = strexists($autoplay, '"true"') ? 'autoplay="autoplay"' : '';
	
	$mp3_html = '';
	$mp3_html .= '<audio class="pure-u-1" '.$autoplay.' controls="controls">';
	$mp3_html .= '<source src="'.$mp3path.'"/>';
	$mp3_html .= '</audio>';
	
	return $mp3_html;
}

function format_row_mp3($row, $column, $type = 'h5'){
	$pattern = "/<[embed |EMBED].*?src=[\'|\"](.*?(?:[\.mp3]))[\'|\"].*?[\/]?>/";
	
	if($type == 'pc') $row[$column] = preg_replace_callback($pattern, "format_row_mp3_pc_parse", $row[$column]);
	else $row[$column] = preg_replace_callback($pattern, "format_row_mp3_h5_parse", $row[$column]);
	return $row;
}

function format_mobile_privacy($mobile){
	return is_mobile($mobile) ? substr($mobile, 0, 3).'**'.substr($mobile, -6) : $mobile;
}

function file_display_order($a, $b){
    if ($a['DISPLAYORDER'] == $b['DISPLAYORDER']) return 0;
    
    return ($a['DISPLAYORDER'] < $b['DISPLAYORDER']) ? -1 : 1;
}

function file_upload_images($filenum){
	global $_var;
	
	$upload_files = array();
	
	foreach($_var['gp_hdnImagePath'] as $key => $filepath){
		if(!$filepath) continue;
		
		$filepath = substr($filepath, 0, 7) == 'FILEID:' ? substr($filepath, 7) : $filepath;
		
		$temparr = explode('|', $filepath);
		$temparr = get_file_stat(format_file_path($temparr[0]));
		
		$upload_files[] = array('FILEPATH' => $filepath, 'DISPLAYORDER' => $_var['gp_txtImageDisplayOrder'][$key] + 0);
		
		unset($temparr);
	}
	
	usort($upload_files, "file_display_order");
	
	$filearr = array();
	for($i = 0; $i < $filenum; $i++) {
		if($upload_files[$i]) $filearr['FILE'.sprintf('%02d', $i + 1)] = $upload_files[$i]['FILEPATH'];
		else $filearr['FILE'.sprintf('%02d', $i + 1)] = '';
	}
	
	return $filearr;
}

function file_clear($object, $filenum){	
	for($i = 1; $i <= $filenum; $i++) {
		if($object['FILE'.sprintf('%02d', $i)]){
			if(is_array($object['FILE'.sprintf('%02d', $i)])){
				
				unlink(ROOTPATH.$object['FILE'.sprintf('%02d', $i)][0]);
				unlink(ROOTPATH.$object['FILE'.sprintf('%02d', $i)][3]);
				
			}else{
				$temparr = explode('|', $object['FILE'.sprintf('%02d', $i)]);
				
				unlink(ROOTPATH.'/attachment/'.$temparr[0]);
				
				unset($temparr);
			}	
		}
	}
}
?>