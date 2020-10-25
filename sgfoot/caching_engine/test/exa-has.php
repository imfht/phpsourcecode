<?php
/**
 * 判断缓存是否存在has($key)
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
$key = 'key-has';
Cache::put($key, 'abc');
if (Cache::has($key)) {
    dump('存在');
} else {
    dump('不存在');
}