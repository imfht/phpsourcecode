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
#$ctrl->token = "mytoken";
$ctrl->topic_mask = Protocol::MASK_DISK;
$ctrl->group_name_auto = true; //动态产生一个消费分组
$ctrl->topic_mask = Protocol::MASK_DISK | Protocol::MASK_EXCLUSIVE; //仅仅允许一个socket连接接入

$c = new Consumer($broker, $ctrl);
$c->messageHandler = $messageHandler;

$c->start();
$loop->run();