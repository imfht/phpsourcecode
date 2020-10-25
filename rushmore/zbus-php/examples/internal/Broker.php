<?php  

require_once '../../zbus.php';
Logger::$Level = Logger::DEBUG; //change to info to disable verbose message

$loop = new EventLoop();

$broker = new Broker($loop, "localhost:15555;localhost:15556");   

$broker->on('serverJoin', function($client){ 
	echo 'server join: ' . $client->serverAddress . PHP_EOL; 
});

$broker->on('serverLeave', function($serverAddress){
	echo 'server leave: ' . $serverAddress . PHP_EOL; 
});

$broker->on('ready', function(){
	echo 'server ready'. PHP_EOL; 
});

$loop->run();