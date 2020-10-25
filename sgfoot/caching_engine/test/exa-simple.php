<?php
/**
 * 简单实例
 * User: freelife2020@163.com
 * Date: 2018/3/26
 * Time: 18:06
 */
require 'helper.php';
require '../autoload.php';

use SgIoc\Cache\Cache;

//缓存配置
$config = array(
    'file'     => array(//文件存储引擎
        'expired'   => 7200,//默认存储时间
        'path'      => __DIR__ . '/storage/',//存储目录,必须可写
        'is_zip'    => 1,//是否开启压缩
        'zip_level' => 6,//压缩等级0~10
    ),
    'memcache' => array(//memcache存储引擎
        'hosts' => array( //支持多台服务器,分布式部署,一个数组代表一个服务器,主机,端口,权重
            array('127.0.0.1', 11211, 33),
        ),//memcached地址
    ),
);

try {
    //注册缓存
    Cache::register($config);
    //键
    $key = 'key-simple';
    //值
    $str = str_repeat(join(',', range('a', 'z')), 1) . '<br/>' . date('H:i:s');
    //判断缓存是否存在
    if(!Cache::has($key)) {
        //写入缓存
        Cache::put($key, $str);
    }
    //读取缓存
    $res = Cache::get($key);
    dump($res);
    //删除缓存
    $bool = Cache::forget($key);
    dump($res);
} catch (Exception $ex) {
    dump($ex->getMessage());
}
