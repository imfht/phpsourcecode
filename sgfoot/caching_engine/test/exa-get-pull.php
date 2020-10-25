<?php
/**
 * get,pull($key)获取缓存
 * User: freelife2020@163.com
 * Date: 2018/3/27
 * Time: 15:56
 */
require 'helper.php';
require '../autoload.php';

use SgIoc\Cache\Cache;

$config = include('config.php');
try {

//注册缓存
    Cache::register($config);
    /***********************************************************************/
    $key = 'key-get-pull';
    /***********************************************************************/
    Cache::put($key, 'abc');
//get 获取缓存,不会删除缓存
    Cache::get($key);

    /***********************************************************************/
//pull 获取缓存后,并删除缓存
    Cache::pull($key);
    dump(Cache::get($key));
} catch (Exception $ex) {
    echo $ex->getMessage();
}
