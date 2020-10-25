<?php
/**
 * remember的使用
 * 缓存不存在则缓存并返回结果,如果存在,直接读取缓存
 * User: freelife2020@163.com
 * Date: 2018/3/27
 * Time: 16:00
 */
require 'helper.php';
require '../autoload.php';
use SgIoc\Cache\Cache;
$config = include('config.php');
//注册缓存
Cache::register($config);
$str = date('H:i:s') . '<br/>';
$str .= 'aaaaaaaaaaaaaaaaaa';
$key = 'key-remember';
$minutes = 10;//10分钟
$rs = Cache::remember($key, $minutes, function () use ($str) {
    return $str . '<br/>bbbbbbbbbbbbbbb';
});
var_dump($rs);



