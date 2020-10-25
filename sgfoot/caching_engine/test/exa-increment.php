<?php
/**
 * 递增increment($key,$step =1)
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
$key = 'key-increment';
dump(Cache::increment($key, 2));