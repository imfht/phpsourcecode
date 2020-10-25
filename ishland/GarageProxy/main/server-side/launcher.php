<?php
echo "[" . date('Y-m-d H:i:s') . "][Main][Init][Info] Initializing...\n";
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Connection\TcpConnection;
use Workerman\Lib\Timer;
require_once __DIR__ . '/Autoloader.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/class/PortChecker.php';
@mkdir(getcwd() . "/logs");
Worker::$stdoutFile = getcwd() . '/logs/latest.log';
Worker::$pidFile = getcwd() . '/.pid';
Worker::$logFile = getcwd() . '/logs/workerman.log';
TcpConnection::$defaultMaxSendBufferSize = 256*1024*1024;
TcpConnection::$maxPackageSize = 256*1024*1024;
$workerid = 0;

allocatePorts();
require_once __DIR__ . '/workers/master.php';
require_once __DIR__ . '/workers/timer.php';

echo "[" . date('Y-m-d H:i:s') . "][Main][Init][Info] Reading Settings...\n";

loadConfig();

echo "[" . date('Y-m-d H:i:s') . "][Main][Init][Info] Configuration has completed.\n";
echo "[" . date('Y-m-d H:i:s') . "][Main][Init][Info] Initializtion has completed.\n";
echo "[" . date('Y-m-d H:i:s') . "][Main][Startup][Info] Launching Workerman...\n";
echo "[" . date('Y-m-d H:i:s') . "][Main][Startup][Info] ";

Worker::runAll();
