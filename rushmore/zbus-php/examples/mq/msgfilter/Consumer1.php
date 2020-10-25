<?php
require_once '../../../zbus.php';

$messageHandler = function($msg, $client){ //where you should focus on
    echo $msg . PHP_EOL;
};


Logger::$Level = Logger::DEBUG; //change to info to disable verbose message
$loop = new EventLoop();
$broker = new Broker($loop, "localhost:15555;localhost:15556"); //HA, test it?!


$ctrl = new MessageCtrl();
$ctrl->topic = "MyTopic";  
$ctrl->consume_group = "PHP_Filter_group1"; 
$ctrl->group_filter = "Stock.A.*";


$c = new Consumer($broker, $ctrl);
$c->connectionCount = 1;
$c->messageHandler = $messageHandler;

$c->start();
$loop->run();