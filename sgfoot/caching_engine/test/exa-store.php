<?php
/**
 * 切换不同的缓存引擎,默认default=file
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

$key = 'key-store';
try{
//切换File引擎
    Cache::store('file')->put($key, 'abc');
    dump(Cache::get($key));

//切换memcache引擎
    Cache::store('memcache')->put($key, 'abc');
    dump(Cache::get($key));
}catch (Exception $ex) {
    echo $ex->getMessage();
}