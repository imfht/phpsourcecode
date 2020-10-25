<?php
error_reporting(E_ALL ^ E_NOTICE);
ob_implicit_flush();
date_default_timezone_set('Asia/Shanghai');

include __DIR__ . '/autoload.php';

$app = new app\Boot('127.0.0.1', 8001);
$app->run();
