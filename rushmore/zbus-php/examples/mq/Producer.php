<?php 
require_once '../../zbus.php';


function biz($broker){ 
	$producer = new Producer($broker);
	$msg = new Message();
	$msg->url = '/abc';
	$msg->topic = 'MyTopic';
	$msg->tag = 'Stock.A.中文';
	$msg->body = 'From PHP sync 中文';
	
	$res = $producer->publish($msg);
	echo $res . PHP_EOL;
}


$loop = new EventLoop(); 
$broker = new Broker($loop, "localhost:15555;localhost:15556", true); // enable sync mode

$broker->on('ready', function() use($loop, $broker){  
	//run after ready
	try {  biz($broker); } catch (Exception $e){ echo $e->getMessage() . PHP_EOL; }
	
	$broker->close();  
	$loop->stop(); //stop anyway
}); 

$loop->runOnce();