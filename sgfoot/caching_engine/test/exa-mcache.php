<?php
/**
 * memcache快捷操作类
 * 相当于Cache::store('memcache')
 * User: freelife2020@163.com
 * Date: 2018/3/27
 * Time: 15:56
 */
require 'helper.php';
require '../autoload.php';

use SgIoc\Cache\Cache;
use SgIoc\Cache\MCache;
$config = include('config.php');
//注册缓存
Cache::register($config);
$rs = MCache::info();
dump($rs);
//MCache::put('key', 'abc');//相当于Cache::store('memcache')->put();
//MCache::get('key');//相当于Cache::store('memcache')->get();