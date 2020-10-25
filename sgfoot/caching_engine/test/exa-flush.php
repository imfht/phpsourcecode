<?php
/**
 * 清理所有缓存
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
//flush清除所有缓存,慎用
$bool = Cache::flush();
dump($bool);