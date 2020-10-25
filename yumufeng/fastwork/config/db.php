<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/2
 * Time: 12:57
 */

return [
    'mysql' => [
        //服务器地址
        'host' => '127.0.0.1',
        //端口
        'port' => 3306,
        //用户名
        'user' => 'root',
        //密码
        'password' => '123456',
        //数据库编码，默认为utf8
        'charset' => 'utf8',
        //数据库名
        'database' => 'mzhua',
        //表前缀
        'prefix' => 'mz_',
        //空闲时，保存的最大链接，默认为5
        'poolMin' => 1,
        //地址池最大连接数，默认1000
        'poolMax' => 1000,
        //清除空闲链接的定时器，默认60s
        'clearTime' => 30,
        //空闲多久清空所有连接,默认300s
        'clearAll' => 300,
        //设置是否返回结果
        'setDefer' => true,
        'reconnect' => 2  //自动连接尝试次数，默认为1次
    ]
];