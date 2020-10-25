<?php 
require_once '../../zbus.php';

$loop = new EventLoop();

$client = new MqClientAsync("localhost:15555", $loop);

$client->on('message', function($msg) use($client){
	echo $msg;
	$client->close();
});

$client->on('connected', function() use($client) {
	echo 'connected' . PHP_EOL; 
	$msg = new Message();
	$msg->cmd = 'server';
	$client->invoke($msg); 
});

$client->connect();

$loop->runOnce();