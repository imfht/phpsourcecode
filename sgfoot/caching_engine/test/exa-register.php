<?php
/**
 * 注册不同的引擎
 * User: freelife2020@163.com
 * Date: 2018/3/27
 * Time: 15:56
 */
require 'helper.php';
require '../autoload.php';

use SgIoc\Cache\Cache;

$config = include('config.php');
//注册所有的缓存
Cache::register($config);
//注册file缓存引擎
Cache::registerFile($config);
//注册memcache引擎
Cache::registerMemcache($config);