<?php
/**
 * pac生成
 * pac.php/5.pac 5为自动随机获取的代理数量
 */
header("content-Type: text/html; charset=utf-8");
$log='';$nowtime=time();
for($i=0;$i<=20;$i++){
    $now=date('Ymd',strtotime('-'.$i.' day',$nowtime));
    if(file_exists('./success_proxy/'.$now.'.log')){
        $log='./success_proxy/'.$now.'.log';break;
    }
}
$max=empty($_GET['max'])?0:intval($_GET['max']);
if($_SERVER['PATH_INFO']){
    $pathinfo=str_replace('.pac','',str_replace('/','',$_SERVER['PATH_INFO']));$max=intval($pathinfo);
}
if(empty($max))$max=5;
$proxy='DIRECT';
if($log){
    $content=file($log);
    $proxyarr='';
    $keys = array_rand($content, $max);
    foreach($keys as $val){
        $v=$content[$val];
        $arr=explode('@',$v);
        if(!empty($arr[0]) && strpos($arr[0],':')>0){
            $proxyarr[]='PROXY '.$arr[0];
        }
    }
    if($proxyarr){
        $proxyarr[]='DIRECT';
        $proxy=implode(';',$proxyarr);
    }
    
}else{
    $proxy='PROXY server01.pac.itzmx.com:25;';
}
$domain=empty($_GET['domain'])?'':$_GET['domain'];
if(empty($domain)){
    $domains=file_get_contents('domain.json');
}else if($domain='null'){
    
}else{
    $darr=explode(',',$domain);
    $domarr=array();
    foreach($darr as $k){
        $domarr[$k]=1;
    }
    $domains=json_encode($domarr);
}
if(empty($domains))$domains='""';

$str='var proxy = "'.$proxy.'";
var domains = '.$domains.';
var direct = "DIRECT;";
var hasOwnProperty = Object.hasOwnProperty;
function FindProxyForURL(url, host) {
    if (host == "www.so.com") {
        return "PROXY 360.itzmx.com:80";
    }
    var suffix;
    var pos = host.lastIndexOf('.');
    while(1) {
        suffix = host.substring(pos + 1);
        if (suffix == "360.cn" && url.indexOf(\'http://\') == 0)return "PROXY 360.itzmx.com:80";
        if (domains && hasOwnProperty.call(domains, suffix)) return proxy;
        if (!domains) return proxy;
        if (pos <= 0) {break;}
        pos = host.lastIndexOf(".", pos - 1);
    }
    return direct;
}';
echo $str;
?>