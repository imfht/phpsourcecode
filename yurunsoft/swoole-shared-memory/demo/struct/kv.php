<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use Yurun\Swoole\SharedMemory\Server;
use Yurun\Swoole\SharedMemory\Client\Client;
use Yurun\Swoole\SharedMemory\Client\Store\KV;

$options = [
    // 这个文件必须，而且不能是samba共享文件
    'socketFile'    =>  '/swoole-shared-memory.sock',
];


$client = new Client($options);
var_dump($client->connect());

$kv = new KV($client);

$obj = new stdClass;

$obj->time = date('Y-m-d H:i:s');
$kv->set('a', $obj);
var_dump($kv->get('a'), $kv->get('b', 1));
var_dump($kv->exists('a'), $kv->exists('b'));
var_dump($kv->count());
var_dump($kv->remove('a'), $kv->remove('b'));
var_dump($kv->count());
