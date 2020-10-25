<?php
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Connection\TcpConnection;
use Workerman\Lib\Timer;

$timer = new Worker();
$timer->count = 1;
$timer->name = "timer";
$timer->onWorkerStart = function($worker) {
    global $masterport;
    $conn_to_master = new AsyncTcpConnection("tcp://127.0.0.1:" . $masterport);
    $conn_to_master->onClose = function ($connection) {
        $connection->reConnect();
        $connection->send(json_encode(Array("action" => "reconn", "worker" => "timer")));
    };
    $conn_to_master->onError = function ($connection_to_server) {
        $connection_to_server->close();
    };
    $conn_to_master->connect();
    $conn_to_master->send(json_encode(Array("action" => "new", "worker" => "timer")));
    Timer::add(1, function() use ($conn_to_master){
        $conn_to_master->send(json_encode(Array("action" => "timer")));
    });
};