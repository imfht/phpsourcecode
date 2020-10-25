<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use Yurun\Swoole\SharedMemory\Server;
use Yurun\Swoole\SharedMemory\Client\Client;
use Yurun\Swoole\SharedMemory\Client\Store\Queue;

$options = [
    // 这个文件必须，而且不能是samba共享文件
    'socketFile'    =>  '/swoole-shared-memory.sock',
];

$client = new Client($options);
var_dump($client->connect());

$queue = new Queue($client);

$queue->push('a', microtime(true));
$queue->push('a', microtime(true));
$queue->push('a', microtime(true));
$queue->push('a', 1,2,3);

var_dump($queue->size('a'));

var_dump('front: ', $queue->front('a'));
var_dump('back: ', $queue->back('a'));

var_dump('array:', $queue->getArray('a'));

$instance = $queue->getInstance('a');

var_dump($instance->count());

echo 'pop:', PHP_EOL;

while($element = $queue->pop('a'))
{
    var_dump($element);
}

var_dump('front: ', $queue->front('a'));
var_dump('back: ', $queue->back('a'));