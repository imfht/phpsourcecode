<?php
/**
 * Created by Wenlong Li
 * User: wenlong
 * Date: 2018/9/20
 * Time: 上午11:46
 */


include '../vendor/autoload.php';
include '../src/Kernel/AgileCore.php';
//设置为yaf
define('APP_PATH', realpath('application'));
$app = \Kernel\AgileCore::init([ realpath('../src')], [realpath('config')]);

\Kernel\AgileCore::setConfigDriver('yaf');
\Kernel\Swoole\SwooleHttpServer::setAppType('yaf');

$container = $app->getContainer();
$container->alias('redis', Redis::class);
//将ServerInterface设置为 HttpServer并且设置别名为http
$http = $container->build( \Kernel\Swoole\SwooleHttpServer::class);
$container->singleton(\Kernel\Server::class, \Kernel\Swoole\SwooleHttpServer::class, 'http');
$container->singleton(\Kernel\Core\Route\IRoute::class, \Kernel\Core\Route\CuteRoute::class, 'route');
$app->serverStart($http);