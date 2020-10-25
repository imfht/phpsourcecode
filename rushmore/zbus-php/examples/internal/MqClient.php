<?php 
require_once '../../zbus.php';
 
Logger::$Level = Logger::INFO;

$client = new MqClient("localhost:15555");    

$msg = new Message();
$msg->topic = "MyTopic";
$msg->body = "From PHP sync";

$res = $client->produce($msg);
echo $res . PHP_EOL;
 