<?php
/**
 * add与put的区别及使用方法
 * 三个参数,key(键值),value(缓存数据),minutes([可选]存储时间/分钟)
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
$key = 'key-add-pull';
//add 的使用,当key存在则返回false,当key不存在时进行缓存并返回true
$bool = Cache::add($key, 'abcdefg', 1);
dump($bool);
/***********************************************************************/
//put 的使用,当key存在时,进行覆盖缓存,返回true,不存在缓存并返回true
$bool = Cache::put($key, 'aaaa', 10);
dump($bool);
/***********************************************************************/