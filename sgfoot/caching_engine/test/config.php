<?php
/**
 * 配置文件
 * User: freelife2020@163.com
 * Date: 2018/3/27
 * Time: 15:53
 */
return array(
    'default'   => 'file',//设置默认引擎
    'file'      => array(//文件存储引擎
        'open'      => true,//开关
        'preFix'    => '',//前缀
        'expired'   => 7200,//默认存储时间
        'path'      => __DIR__ . '/storage/',//存储目录,必须可写
        'is_zip'    => 0,//是否开启压缩
        'zip_level' => 6,//压缩等级0~10
    ),
    'memcache'  => array(//memcache存储引擎
        'open'    => true,//开关
        'preFix'  => '',//前缀
        'hosts'   => array( //支持多台服务器,分布式部署,一个数组代表一个服务器,主机,端口,权重
            array('127.0.0.1', 11211, 33),
            array('127.0.0.2', 11211, 33),
            array('127.0.0.3', 11211, 33),
        ),//memcached地址
        'timeout' => 3,//超时设置
        'is_zip'  => 0,//是否开启压缩
    ),
    'memcached' => array(//memcached存储引擎
        'open'    => true,//开关
        'preFix'  => '',//前缀
        'hosts'   => array( //支持多台服务器,分布式部署,一个数组代表一个服务器,主机,端口,权重
            array('127.0.0.1', 11211, 33),
            array('127.0.0.2', 11211, 33),
            array('127.0.0.3', 11211, 33),
        ),//memcached地址
        'timeout' => 3,//超时设置
        'is_zip'  => 0,//是否开启压缩
    ),
    'redis'     => array(
        'open'    => true,//开关
        'preFix'  => 'redis:',//前缀
        'host'    => array('127.0.0.1', 6570),
        'auth'    => '',//连接密码
        'expired' => 7200,//默认存储(秒)
    ),
);