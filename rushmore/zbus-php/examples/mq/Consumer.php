<?php   
require_once '../../zbus.php';

$messageHandler = function($msg, $client){ //where you should focus on  
	$str = json_encode($msg, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
	echo $str . PHP_EOL;
	echo $msg . PHP_EOL;
};


Logger::$Level = Logger::DEBUG; //change to info to disable verbose message
$loop = new EventLoop(); 
$broker = new Broker($loop, "localhost:15555;localhost:15556"); //HA, test it?!
 

$ctrl = new MessageCtrl();
$ctrl->topic = "MyTopic"; 
$ctrl->topic_mask = Protocol::MASK_DISK;
//$ctrl->group_name_auto = true;
//$ctrl->consume_group = "MyTopic_Group12";
//$ctrl->group_mask = Protocol::MASK_DISK;
//$ctrl->group_filter = "abc.*";  


$c = new Consumer($broker, $ctrl);  
$c->connectionCount = 1;
$c->messageHandler = $messageHandler; 

$c->start();  
$loop->run();