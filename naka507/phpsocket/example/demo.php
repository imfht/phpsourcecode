<?php
require __DIR__ . '/../vendor/autoload.php';

use Naka507\Socket\Server;
$server = new Server();

//服务启动
$server->onWorkerStart = function($worker)
{
    echo "New onWorkerStart\n";
};

//建立连接
$server->onConnect = function($connection)
{
    echo "New Connection\n";
};

//接受请求
$server->onMessage = function($request, $response)
{
    $response->write(' Hello World !!!');
    $response->end();
};
$server->start();

