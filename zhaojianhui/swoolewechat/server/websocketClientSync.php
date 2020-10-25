<?php
//载入初始化文件
require_once __DIR__ . '/initServer.php';


//同步socket客户端
$client = new Swoole\Client\WebSocket('127.0.0.1', 9443, '/');
if(!$client->connect())
{
    echo "connect to server failed.\n";
    exit;
}
while (true)
{
    $client->send("hello world");
    $message = $client->recv();
    if ($message === false)
    {
        break;
    }
    echo "Received from server: {$message}\n";
    sleep(1);
}
echo "Closed by server.\n";