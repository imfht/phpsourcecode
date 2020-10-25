<?php
/**
 * 使用配置文件操作缓存
 * User: freelife2020@163.com
 * Date: 2018/3/27
 * Time: 15:56
 */
require 'helper.php';
require '../autoload.php';
use SgIoc\Cache\Cache;

//读取配置文件
$config = include('config.php');
//注册缓存
Cache::register($config);
/***********************************************************************/

//写入缓存
$key = 'my-key';
Cache::put($key, 'abc');

/***********************************************************************/
//读取缓存
$res = Cache::get($key);
dump($res);
