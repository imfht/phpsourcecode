<?php
require_once 'py.class.php';		//载入汉字转拼音类
require_once 'Snoopy.class.php';		//载入史努比
require_once 'version.php';		//载入版本信息
//模版载入及标签替换
function HtmlTag($d){
	global $info;
	global $isversion;
	@$Html=file_get_contents($d);
	if($Html){
		foreach($info as $key=>$value){
			$Html=str_replace("{".$key."}",$value,$Html);
		}
		$Html=str_replace("{version}",$isversion,$Html);
		//$Html=str_replace(PHP_EOL,"",$Html);	//去除换行
	}else{
		$Html="/*****************************\n!!!载入文件（".$d."）失败!!!请检查路径是否正确!!!\n*****************************/";
	}
	echo $Html."\n";
}
//判断浏览器类型
function my_get_browser(){
	if(empty($_SERVER['HTTP_USER_AGENT'])){
		return 'no';
	}
	if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'MSIE')){
		return 'IE';
	}
	if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'Firefox')){
		return 'Firefox';
	}
	if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'Chrome')){
		return 'Chrome';
	}
	if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'Safari')){
		return 'Safari';
	}
	if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'Opera')){
		return 'Opera';
	}
	if(false!==strpos($_SERVER['HTTP_USER_AGENT'],'360SE')){
		return '360SE';
	}
}
//判断是否手机访问
function is_mobile_request(){
	$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
	$mobile_browser = '0';
	if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
	$mobile_browser++;
	if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))
	$mobile_browser++;
	if(isset($_SERVER['HTTP_X_WAP_PROFILE']))$mobile_browser++;
	if(isset($_SERVER['HTTP_PROFILE']))$mobile_browser++;
	$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
	$mobile_agents = array(
		'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac','blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno','ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-','maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-','newt','noki','oper','palm','pana','pant','phil','play','port','prox','qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar','sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-','tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp','wapr','webc','winw','winw','xda','xda-'
	);
	if(in_array($mobile_ua, $mobile_agents))$mobile_browser++;
	if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)$mobile_browser++;
	if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)$mobile_browser=0;
	if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)$mobile_browser++;
	if($mobile_browser>0)
		return true;
	else
		return false;
}
//过滤非法字符
function safe_string($str){ 
	$str=str_replace("'","",$str);
	$str=str_replace('"',"",$str);
	$str=str_replace(" ","$nbsp;",$str);
	$str=str_replace("\n;","<br/>",$str);
	$str=str_replace("<","<",$str);
	$str=str_replace(">",">",$str);
	$str=str_replace("\t"," ",$str);
	$str=str_replace("\r","",$str);
	$str=str_replace("/[\s\v]+/"," ",$str);
	return $str;
}
//相对连接转补全为绝对连接方法	
function formaturl($gethtml, $geturl) {
	if (preg_match_all("/(<img[^>]+src=\"([^\"]+)\"[^>]*>)|(<a[^>]+href=\"([^\"]+)\"[^>]*>)|(<img[^>]+src='([^']+)'[^>]*>)|(<a[^>]+href='([^']+)'[^>]*>)|(<script[^>]+src=\"([^\"]+)\"[^>]*>)|(<link[^>]+href=\"([^\"]+)\"[^>]*>)/i", $gethtml, $regs)) {
		foreach ($regs[0] as $num => $url) {
			$gethtml = str_replace($url, regtest($url, $geturl) , $gethtml);
		}
	}
	return $gethtml;
}
function regtest($gethtml, $geturl) {
	if (preg_match("/(.*)(href|src)\=(.+?)( |\/\>|\>).*/i", $gethtml, $regs)) {
		$I2 = $regs[3];
	}
	if (strlen($I2) > 0) {
		$I1 = str_replace(chr(34) , "", $I2);
		$I1 = str_replace(chr(39) , "", $I1);
	} else {
		return $gethtml;
	}
	$url_parsed = parse_url($geturl);
	$scheme = $url_parsed["scheme"];
	if ($scheme != "") {
		$scheme = $scheme . "://";
	}
	$host = $url_parsed["host"];
	$l3 = $scheme . $host;
	if (strlen($l3) == 0) {
		return $gethtml;
	}
	$path = dirname($url_parsed["path"]);
	if ($path[0] == "\\") {
		$path = "";
	}
	$pos = strpos($I1, "#");
	if ($pos > 0) $I1 = substr($I1, 0, $pos);
	//判断类型
	if (preg_match("/^(http|https|ftp):(\/\/|\\\\)(([\w\/\\\+\-~`@:%])+\.)+([\w\/\\\.\=\?\+\-~`@\':!%#]|(&)|&)+/i", $I1)) {
		return $gethtml;
	} //跳过http开头的url类型
	elseif ($I1[0] == "/") {
		$I1 = $l3 . $I1;
	}
	elseif (substr($I1, 0, 3) == "../") {
		while (substr($I1, 0, 3) == "../") {
			$I1 = substr($I1, strlen($I1) - (strlen($I1) - 3) , strlen($I1) - 3);
			if (strlen($path) > 0) {
				$path = dirname($path);
			}
		}
		$I1 = $l3 . $path . "/" . $I1;
	} elseif (substr($I1, 0, 2) == "./") {
		$I1 = $l3 . $path . substr($I1, strlen($I1) - (strlen($I1) - 1) , strlen($I1) - 1);
	} elseif (strtolower(substr($I1, 0, 7)) == "mailto:" || strtolower(substr($I1, 0, 11)) == "javascript:") {
		return $gethtml;
	} else {
		$I1 = $l3 . $path . "/" . $I1;
	}
	return str_replace($I2, "\"$I1\"", $gethtml);
}
?>