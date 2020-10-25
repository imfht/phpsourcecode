<?php
date_default_timezone_set('Asia/Shanghai');

define('ROOT_PATH', __DIR__);

include 'Framework/SZLoader.php';

$host = '0.0.0.0';
$port = 9502;

Framework\SZLoader::init();
$server = Framework\SZServer::Instance($host, $port);
$server->run();