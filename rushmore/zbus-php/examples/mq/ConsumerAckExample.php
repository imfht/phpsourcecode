<?php   
require_once '../../zbus.php';

$messageHandler = function($msg, $client){ //where you should focus on  
	echo $msg . PHP_EOL;
	$client->ack($msg);
};


Logger::$Level = Logger::DEBUG; //change to info to disable verbose message
$loop = new EventLoop(); 
$broker = new Broker($loop, "localhost:15555;localhost:15556"); //HA, test it?!
 

$ctrl = new MessageCtrl();
$ctrl->topic = "MyTopic";  
$ctrl->consume_group = "MyTopic_ACK_Group1"; 
$ctrl->group_mask = Protocol::MASK_ACK_REQUIRED;
$ctrl->group_ack_timeout = 10*1000; //milliseconds by default


$c = new Consumer($broker, $ctrl);  
$c->consumeTimeout = 10;
$c->connectionCount = 1;
$c->messageHandler = $messageHandler; 

$c->start();  
$loop->run();