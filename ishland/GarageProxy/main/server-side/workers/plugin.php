<?php
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Connection\TcpConnection;
use Workerman\Lib\Timer;

$plugin = new Worker('tcp://127.0.0.1:4401');