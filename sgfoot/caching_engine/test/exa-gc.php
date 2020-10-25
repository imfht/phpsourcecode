<?php
/**
 * memcache 垃圾回收机制
 * User: Administrator
 * Date: 2018/4/9
 * Time: 15:07
 */

require 'helper.php';
require '../autoload.php';

use SgIoc\Cache\MemcacheGc;

$config = include('config.php');
$mem    = new MemcacheGc('127.0.0.1', 11211);
$info   = $mem->info();//查看所有的key
dump($info);
//回收过期的元素
$mem->gc();