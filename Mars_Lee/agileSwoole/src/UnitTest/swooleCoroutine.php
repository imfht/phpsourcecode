<?php

$serverConfig = [
        'host'          =>      '0.0.0.0',
        'port'          =>      9550,
        'mode'          =>      SWOOLE_PROCESS,
        'type'          =>      SWOOLE_SOCK_TCP
];
$server = new swoole_http_server($serverConfig['host'],$serverConfig['port'],$serverConfig['mode']);
$server->set($swooleOption = [
        'worker_num' =>  4,    //开启两个worker进程
        'daemonize'  => true,
        'max_request' => 5000,   //每个worker进程max request设置为3次
        'dispatch_mode'=> 3,
        'open_eof_check' => true, //打开EOF检测
        'package_eof' => PHP_EOL, //设置EOF
        'open_cpu_affinity'     =>      true,
        'user'  =>      'www',   //设置运行用户
        'group' =>      'www',
        'buffer_output_size' => 32 * 1024 *1024, //必须为数字  输出缓存
        'socket_buffer_size' => 128 * 1024 *1024, //必须为数字 内存缓存
]);
$server->on('request',function (swoole_http_request $request, swoole_http_response $response){
        Swoole\Coroutine::create(function () use($response){
                $response->end('hello world');
        });
});
$server->start();