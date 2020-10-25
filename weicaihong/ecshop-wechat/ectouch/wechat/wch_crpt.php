<?php

//引入配置文件
require_once 'wch_config.php';

if(!isset($appId)){
    echo 'appId 不存在';
    exit;
}
if(!empty($_GET['act']))
{
    if($_GET['act'] == 'debug')
    {
        $wch_debug = array('appid'=>$appId);
        $wch_debug['wch_ecshop_version'] = array('wch_ecshop_version'=>$wch_ecshop_version);
        $wch_debug['curl'] = wch_fun('curl_init');
        $wch_debug['pdo_mysql'] = wch_ext('pdo_mysql');
        echo json_encode($wch_debug);
        exit;
    }
}




//定义appid(加解密时使用)
define("appId",$appId);

/*
 * $data 需要加密的数据
 * $key  加密附加字符串
 * */
function wch_encrypt($data, $key=''){
    //默认使用appid为key值
    if($key == ''){
        $key = constant("appId");
    }
    $key = md5($key);
    $x  = 0;
    $len = strlen($data);
    $l  = strlen($key);
    $char = '';
    for ($i = 0; $i < $len; $i++)
    {
        if ($x == $l)
        {
            $x = 0;
        }
        $char .= $key{$x};
        $x++;
    }
    $str = '';
    for ($i = 0; $i < $len; $i++)
    {
        $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
    }
    return base64_encode($str);
}


/*
 * $data 需要加密的数据
 * $key  加密附加字符串
 * */
function wch_decrypt($data, $key='')
{
    //默认使用appid为key值
    if($key == ''){
        $key = constant("appId");
    }
    $key = md5($key);
    $x = 0;
    $data = base64_decode($data);
    $len = strlen($data);
    $l = strlen($key);
    $char = '';
    for ($i = 0; $i < $len; $i++)
    {
        if ($x == $l)
        {
            $x = 0;
        }
        $char .= substr($key, $x, 1);
        $x++;
    }
    $str = '';
    for ($i = 0; $i < $len; $i++)
    {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1)))
        {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }
        else
        {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return $str;
}


//验证token正确性
function verifyToken($token)
{
    $key = urlencode(md5(constant("appId")));
    if($token == $key){
        return 1;
    }else{
        return 0;
    }
}

// 函数支持验证
function wch_fun($funName = '')
{
    if(function_exists($funName))
    {
        return 'yes';
    }
    else
    {
        return 'no';
    }
}

// 模块支持检测
function wch_ext($funName = '')
{
    if (extension_loaded($funName))
    {
        return 'yes';
    }
    else
    {
        return 'no';
    }
}



?>