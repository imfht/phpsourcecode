<?php

//主队列
$rabbitmq['master'] = [
    'host'    => '172.17.0.4', //主机地址
    'port'    => 5672, //端口
    'user'    => 'guest', //用户
    'pass'    => 'guest', //密码
    'vhost'   => '/',
    'debug'   => false, //是否开启调试模式
];
//事件队列
$rabbitmq['event'] = [
    'host'    => '172.17.0.4', //主机地址
    'port'    => 5672, //端口
    'user'    => 'guest', //用户
    'pass'    => 'guest', //密码
    'vhost'   => '/',
    'debug'   => false, //是否开启调试模式
];

return $rabbitmq;
