<?php
use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Lib\Timer;
use PHPSocketIO\SocketIO;

// composer 的 autoload 文件
include __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/vendor/workerman/phpsocket.io/src/autoload.php';


// 全局数组保存uid在线数据
$uidConnectionMap = array();
// 记录最后一次广播的在线用户数
$last_online_count = 0;
// 记录最后一次广播的在线页面数
$last_online_page_count = 0;

// 启动一个webserver，用于吐html css js，方便展示
// 这个webserver服务不是必须的，可以将这些html css js文件放到你的项目下用nginx或者apache跑
$web = new WebServer('http://0.0.0.0:2123');
$web->addRoot('localhost', __DIR__ . '/web');

// 运行所有的服务
Worker::runAll();
