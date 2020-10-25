<?php
require 'vendor/autoload.php';

use Workerman\Lib\Timer;
use Workerman\Worker;



$worker = new Worker('tcp://0.0.0.0:8079');

$worker->onWorkerStart  = function(){
    Worker::safeEcho('555');
    $worker = new Worker('tcp://0.0.0.0:8088');
    $worker->onWorkerStart = function(){
        Worker::safeEcho('333');
        $workera = new Worker('tcp://0.0.0.0:8099');
        $workera->onWorkerStart = function(){
            Worker::safeEcho('444');
            $workera = new Worker('tcp://0.0.0.0:8069');
            $workera->onWorkerStart = function(){
                Worker::safeEcho('444');
                
            };
            // $workera->listen();
            // $workera->run();
        };
        // $workera->listen();
        // $workera->run();
    };
    // $worker->listen();
    // $worker->run();
    
    Timer::add(1,function(){
        
        
    });
};

Worker::runAll();