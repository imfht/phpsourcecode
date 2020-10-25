<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/30
 * Time: 12:40
 */

return [
    'redis' => [
        'poolMin' => 1, //连接池最小数量 数据为：work_num * poolMin
        'poolMax' => 1000, // 连接池最大数量
        'host' => '127.0.0.1',
        'auth' => '', //密码
        'port' => 6379,
        'prefix' => 'mzhua_',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
        //清除空闲链接的定时器，默认120s
        'clearTime' => 60,
        //空闲多久清空所有连接,默认300s
        'clearAll' => 300,
        //设置是否返回结果
        'setDefer' => true,
        //options配置
        'connect_timeout' => 2, //连接超时时间，默认为1s
        'timeout' => 5, //超时时间，默认为1s
        //是否序列化
        'serialize' => true, //自动序列化，默认false
        'reconnect' => 2  //自动连接尝试次数，默认为1次
    ]
];