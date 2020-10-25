<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use Yurun\Swoole\SharedMemory\Server;
use Yurun\Swoole\SharedMemory\Client\Client;
use Yurun\Swoole\SharedMemory\Client\Store\Stack;

$options = [
    // 这个文件必须，而且不能是samba共享文件
    'socketFile'    =>  '/swoole-shared-memory.sock',
];


$client = new Client($options);
var_dump($client->connect());

$stack = new Stack($client);

$stack->push('a', microtime(true));
$stack->push('a', microtime(true));
$stack->push('a', microtime(true));
$stack->push('a', 1,2,3);

var_dump($stack->size('a'));

var_dump('top: ', $stack->top('a'));

var_dump('array:', $stack->getArray('a'));

$instance = $stack->getInstance('a');

var_dump($instance->count());

echo 'pop:', PHP_EOL;

while($element = $stack->pop('a'))
{
    var_dump($element);
}

var_dump('top: ', $stack->top('a'));