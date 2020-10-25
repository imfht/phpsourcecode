<?php

include '../../vendor/autoload.php';
include '../../src/Kernel/AgileCore.php';
$app = \Kernel\AgileCore::init([ realpath('../../src'), realpath('../app')], [realpath('../conf')]);
$container = $app->getContainer();
$container->alias('redis', Redis::class);
//将ServerInterface设置为 HttpServer并且设置别名为http
$container->singleton(\Kernel\Server::class, \Kernel\Swoole\SwooleHttpServer::class, 'http');
//set default route
$container->singleton(\Kernel\Core\Route\IRoute::class, \Kernel\Core\Route\CuteRoute::class, 'route');
/* @var \Kernel\Swoole\SwooleHttpServer $http */
$http = $app->get('http');
$http->createTable('crawler');
$app->serverStart($http);
