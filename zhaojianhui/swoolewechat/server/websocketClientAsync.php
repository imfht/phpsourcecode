<?php
//载入初始化文件
require_once __DIR__ . '/initServer.php';

//异步socket客户端

//$cli = new Swoole\Client\WebSocket('127.0.0.1', 9443);
//$res = $cli->connect();
////测试各种大包
//for($i=0; $i< 1; $i++)
//{
//    $_send = str_repeat('A', rand(7000, 90000));
//    $n = $cli->send($_send);
//    echo "sent: ".strlen($_send).' bytes, '."n=$n\n";
//    $frame = $cli->recv();
//    echo "recv: ".strlen($frame->data)." bytes\n";
//}

$i = 0;
$cli = new Swoole\Async\WebSocket('127.0.0.1', 9443);

$cli->on('open', function(Swoole\Async\WebSocket $o, $header){
    $_send = str_repeat('A', rand(700, 900));
    $n = $o->send($_send);
    echo "sent: " . strlen($_send) . ' bytes, ' . "n=$n\n";
});

$cli->on('message', function(Swoole\Async\WebSocket $o, $frame){
    global $i;
    echo "$i\trecv: ".strlen($frame->data)." bytes\n";
    $i++;
});

$cli->connect();