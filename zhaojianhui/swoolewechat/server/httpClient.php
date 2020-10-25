<?php
//载入初始化文件
require_once __DIR__ . '/initServer.php';

$client = new Swoole\Async\HttpClient('http://127.0.0.1:8888/post.php?hello=world');
$client->onReady(function($cli, $body, $header){
    var_dump($body, $header);
});
$client->post(['hello' => 'world','test'=>1]);

/*$client = new Swoole\Async\HttpClient('https://www.baidu.com/');
$client->onReady(function($cli, $body, $header){
    var_dump($body, $header);
});
$client->get();*/
//$client->close();