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
if(empty($param['ipaurl']) || empty($param['ipaurl'])){
    header("content-Type: text/html; charset=utf-8");
    exit('?ipaurl=http://www.domain.com/test.ipa&bundleid=com.hanchao.app&imgurl=http://www.bitefu.net/ipa/57x57.png&version=1.0.1&title=ipa');
}
include('Ipainstall.class.php');
$ipa =new Ipainstall;
$ipaurl=$ipa->getplist($param); 
//gzip输出   
if (extension_loaded('zlib')) {if (!headers_sent() AND isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {ob_start('ob_gzhandler');}}  
require_once('template/index.php');//加载模板
ob_end_flush();

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