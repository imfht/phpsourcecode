<?php
/**
 * 永久缓存forever($key, $value/closure)支持匿名函数
 * User: freelife2020@163.com
 * Date: 2018/3/27
 * Time: 15:56
 */
require 'helper.php';
require '../autoload.php';

use SgIoc\Cache\Cache;

$config = include('config.php');
//注册缓存
Cache::register($config);
/***********************************************************************/
$key = 'key-forever';
/***********************************************************************/
//forever的使用
$bool = Cache::forever($key, 'abc');
dump($bool);
dump(Cache::get($key));
/***********************************************************************/
//forever的使用,使用匿名函数
$bool = Cache::forever($key, function () {
    return range('a', 'z');
});
dump($bool);
dump(Cache::get($key));
/***********************************************************************/