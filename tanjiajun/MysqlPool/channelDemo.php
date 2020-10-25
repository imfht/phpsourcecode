<?php
/**
 * 协程channelDemo
 * @author tanjiajun
 */

use \Swoole\Coroutine\Channel;

$chan = new Channel();
go(function () use ($chan) {
    echo "我是第一个协程，等待3秒内有push就执行返回" . PHP_EOL;
    $p = $chan->pop(2);#1
    echo "pop返回结果" . PHP_EOL;
    var_dump($p);
});
go(function () use ($chan) {
    co::sleep(3);#2
    $chan->push(1);
});
echo "main" . PHP_EOL;