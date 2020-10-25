<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use Yurun\Swoole\SharedMemory\Server;
use Yurun\Swoole\SharedMemory\Client\Client;
use Yurun\Swoole\SharedMemory\Client\Store\PriorityQueue;

$options = [
    // 这个文件必须，而且不能是samba共享文件
    'socketFile'    =>  '/swoole-shared-memory.sock',
];

$client = new Client($options);
var_dump($client->connect());

$queue = new PriorityQueue($client);

$queue->insert('a', microtime(true), 999);
$queue->insert('a', microtime(true), 777);
$queue->insert('a', microtime(true), 888);

var_dump($queue->size('a'));

$array = $queue->getArray('a');

var_dump($array);

$instance = $queue->getInstance('a');

var_dump($instance->count());

while($element = $queue->extract('a'))
{
    var_dump($element);
}
