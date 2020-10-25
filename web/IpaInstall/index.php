<?php
/**
 * ipa下载安装
 * @Copyright (C) 2018 汉潮 All rights reserved.
 * @License http://www.hanchao9999.com
 * @Author xiaogg <xiaogg@sina.cn>
 */
$param=array(
    'ipaurl'=>getvar('ipaurl'),
    'bundleid'=>getvar('bundleid'),
    'imgurl'=>getvar('imgurl','http://www.bitefu.net/ipa/57x57.png'),
    'title'=>getvar('title','IOS安装'),
    'version'=>getvar('version','1.0'),
);
$dir='ipa/';//当前文件包在目录
if(empty($param['ipaurl']) || empty($param['ipaurl'])){
    header("content-Type: text/html; charset=utf-8");
    exit('?ipaurl=http://www.domain.com/test.ipa&bundleid=com.hanchao.app&imgurl=http://www.bitefu.net/ipa/57x57.png&version=1.0.1&title=ipa');
}
$xml=file_get_contents('template/plist.xml');//加载plist模板
$content=replacecontent($xml,$param);//批量替换变量
$name=S($param['bundleid'],$content);//缓存
$ipaurl='https://'.$_SERVER['HTTP_HOST'].'/'.$dir.$name; 
//gzip输出   
if (extension_loaded('zlib')) {if (!headers_sent() AND isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {ob_start('ob_gzhandler');}}  
require_once('template/index.php');//加载模板
ob_end_flush();
/**
 * 缓存
 * @param $key 缓存名
 * @param $value 缓存值 为空时为 读,不为空为写
 */
function S($key,$value=''){
    if(is_array($key))$key=md5(json_encode($key));
    $filename='cache/'.$key.'.plist';
    file_put_contents($filename,$value);clearcache();
    return $filename;
}
//清空所有缓存
function clearcache(){
   $cachepath="cache/";
   $date=date('Y-m-d');$cachename='cachetime'.$date.'.c';
   if(file_exists($cachepath.$cachename))return false;
   foreach(scandir($cachepath) as $fn) {
	unlink($cachepath.$fn);
   }file_put_contents($cachepath.$cachename,'1');
   return true;
}
/**
 * 批量替换变量
 * @param $content 模板
 * @param $param 替换的变量及值数组
 */
function replacecontent($content,$param){
    $resplace=array('ipaurl','bundleid','imgurl','title','version');
    $rep=array();
    foreach($resplace as $v){$rep['['.$v.']']=$param[$v];}
    return strtr($content,$rep);
}
/**
 * 获取提交的参数
 * @param $name 变量名
 * @param $default 当获取到的值为空时 返回默认值
 */
function getvar($name,$default=false){
    global $_GET, $_POST;
    if (isset($_GET[$name])) return $_GET[$name];
    else if (isset($_POST[$name])) return $_POST[$name];
    else return $default;
}
?>