<?php

$redis['master'] = [
    'host'     => '172.17.0.3',
    'port'     => 6379,
    'password' => '',
    'timeout'  => 0.25,
    'pconnect' => false,
//    'database' => 1,
];
$redis['event'] = [
    'host'     => '172.17.0.3',
    'port'     => 6379,
    'password' => '',
    'timeout'  => 0.25,
    'pconnect' => false,
//    'database' => 1,
];

return $redis;
