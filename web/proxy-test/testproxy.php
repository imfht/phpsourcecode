<?php
header("content-type:text/html; charset=utf-8");
$ip=empty($_GET['ip'])?'':$_GET['ip'];
if(empty($ip) || strpos($ip,'.')==-1){$result=array('status'=>0,'info'=>'ip is error');result($result);exit;}
$startTime = microtime(true);
$port=empty($_GET['port'])?'80':$_GET['port'];
$type=empty($_GET['type'])?'':$_GET['type'];
$timeout=empty($_GET['timeout'])?'5':$_GET['timeout'];
$url=empty($_GET['urltype'])?'':'https://www.so.com/robots.txt';

$file_contents=curltest($ip,80,$type,$url,$timeout);

if(strpos($file_contents,'Disallow')>0){
    $result=array('status'=>1,'info'=>'test success');
    $str=$ip.':'.$port.'@'.($type?'SOCKS5':'HTTP');
    save_success_proxy($str);
}else{
    $result=array('status'=>0,'info'=>'test error');
}
$endTime = microtime(true);
$result['time']=(($endTime - $startTime)*1000);
result($result);
function curltest($ip,$port=80,$type='',$url='',$timeout = 10){    
    if(empty($url))$url = 'https://www.baidu.com/robots.txt';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
    curl_setopt($ch, CURLOPT_PROXY, $ip); //代理服务器地址
    curl_setopt($ch, CURLOPT_PROXYPORT, $port); //代理服务器端口
    //curl_setopt($ch, CURLOPT_PROXYUSERPWD, ":"); //http代理认证帐号，username:password的格式
    if(empty($type))curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
    else curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
}
function result($array){
    $content=json_encode($array);
    if(empty($_GET['callback'])){
        echo $content;exit;
    }else{
        echo $_GET['callback']."(".$content.")";exit;
    }	   
}
function save_success_proxy($str){
	if(empty($str))return;$str=trim($str);
    $filename='./success_proxy/'.date('Ymd').'.log';
    if(file_exists($filename)){
        $content=file_get_contents($filename);
        if(strpos('a'.$content,$str)>0)return true;
    }    
	$fp = fopen($filename, 'a+');
	fwrite($fp, $str."\n");
	fclose($fp);
	unset($str);
}
?>