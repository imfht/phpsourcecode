<?php
require __DIR__ . '/../vendor/autoload.php';

use Naka507\Socket\Server;
$opt = array(
    'ssl' => array(
        // 请使用绝对路径
        'local_cert'                 => '/***/fullchain.pem',
        'local_pk'                   => '/***/privkey.pem',
        'verify_peer'                => false,
        'allow_self_signed' 		 => true 
    )
);
$server = new Server(443,$opt);
$server->transport = 'ssl';

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
    $response->write(' SSL: Hello World !!!');
    $response->end();
};
$server->start();



