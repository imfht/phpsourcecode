<?php
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Connection\TcpConnection;
require_once __DIR__ . '/Autoloader.php';
@mkdir(getcwd() . "/logs");
Worker::$stdoutFile = getcwd() . '/logs/latest.log';
Worker::$pidFile = getcwd() . '/.pid';
Worker::$logFile = getcwd() . '/logs/workerman.log';
TcpConnection::$defaultMaxSendBufferSize = 256*1024*1024;
TcpConnection::$maxPackageSize = 256*1024*1024;

if (! file_exists(getcwd() . "/config.php")) {
    echo "[" . date('Y-m-d H:i:s') . "][Main][Init][Info] Configration not found, create one.\n";
    file_put_contents(getcwd() . "/config.php", file_get_contents(__DIR__ . "/defaults/config.php"));
}
require_once getcwd() . "/config.php";

$worker = new Worker(LISTENING_ADDRESS);
$worker->count = 2;
$worker->name = "Client";
$worker->onWorkerStart = function($worker){
    echo "Worker started.\n";
};
$worker->onConnect = function($connection){
    echo "New connection.\n";
    $connection->firstmsg = true;
    $connection_to_server = new AsyncTcpConnection(SERVER_ADDRESS);
    $connection_to_server->onMessage = function ($connection_to_server, $buffer) use ($connection) {
        if($connection->firstmsg == true){
            if($buffer == "GarageProxy-OK"){
                echo "Connected and verified the connection to the server.\n";
                $connection->firstmsg = false;
                return;
            } else {
                echo "Connected but can not verify the connection to the server. Close.\n";
                $connection->close();
                $connection_to_server->close();
            }
        }
        if(USE_COMPRESSION == true) $connection->send(gzinflate($buffer));
        else $connection->send($buffer);
    };
    $connection_to_server->onClose = function ($connection_to_server) use ($connection) {
        echo "The connection to the server closed.\n";
        $connection->close();
    };
    $connection_to_server->onError = function ($connection_to_server, $errcode, $errmsg) use ($connection) {
        echo "The connection to the server has a error. Closing connection...\n";
        $connection->close();
    };
    $connection_to_server->onBufferFull = function ($connection_to_server) use ($connection){
        echo "The connection to the server's buffer is full.\n";
        $connection_to_server->pauseRecv();
    };
    $connection_to_server->onBufferDrain = function ($connection_to_server) use ($connection){
        echo "The connection to the server's buffer is drain.\n";
        $connection_to_server->resumeRecv();
    };
    
    echo "Connecting...\n";
    $connection_to_server->connect();
    echo "Verifying the connection to the server...\n";
    $connection_to_server->send("GarageProxyClient" . json_encode(Array("compression" => USE_COMPRESSION)));
    
    $connection->onMessage = function ($connection, $buffer) use ($connection_to_server) {
        if(USE_COMPRESSION == true) $connection_to_server->send(gzdeflate($buffer, 9));
        else $connection_to_server->send($buffer);
    };
    $connection->onClose = function ($connection) use ($connection_to_server) {
        echo "The connection to the client closed.\n";
        $connection_to_server->close();
    };
    $connection->onError = function ($connection, $errcode, $errormsg) use ($connection_to_server) {
        echo "The connection to the client has a error. Closing connection...\n";
        $connection_to_server->close();
    };
    $connection->onBufferFull = function ($connection) use ($connection_to_server){
        echo "The connection to the client's buffer is full.\n";
        $connection->pauseRecv();
    };
    $connection->onBufferDrain = function ($connection) use ($connection_to_server){
        echo "The connection to the client's buffer is drain.\n";
        $connection->resumeRecv();
    };
};

Worker::runAll();