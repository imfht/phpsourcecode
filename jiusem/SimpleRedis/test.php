<?php

require __DIR__ . '/vendor/autoload.php';

$config = [
    'host' => '111.229.224.113',
    'port' => '6379',
    'password' => 'EpJ6Gy8s7CPjnvzrzPKrxvJlKfB53JlN',
];
$connection = new \Crazymus\SimpleRedis\Connection($config);
$client = new \Crazymus\SimpleRedis\Client($connection);

var_dump($client->set('foo', '123444' . PHP_EOL . '456'));
var_dump($client->get('foo'));


