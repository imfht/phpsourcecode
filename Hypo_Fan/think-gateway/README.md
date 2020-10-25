# think-gateway
ThinkPHP 5 GatewayWorker 扩展
===============

## 安装
composer require hypo-fan/think-gateway

## 使用方法
首先创建控制器类并继承 HypoFan\gateway\GatewayServer，然后设置属性、Worker和添加回调方法

~~~
namespace app\gateway\controller;

use think\worker\GatewayServer;
use GatewayWorker\Lib\Gateway;

class Worker extends GatewayServer {
	
	protected function start() {
		$this->setRegister('RegisterWorker', 1, '1238', '0.0.0.0');
		$this->setGateway('text://0.0.0.0:4399', 'GatewayWorker', [
			'lanIp' => '127.0.0.1',
			'startPort' => '2000',
			'registerAddress' => '127.0.0.1:1238'
		]);
		$this->setBusiness('BusinessWorker', [
			'registerAddress' => '127.0.0.1:1238'
		]);
	}
	
	/**
	 * @see parent::onMessage
	 */
	public static function onMessage(string $client_id, $data) {
		//do something......
	}
	
}
~~~
支持的回调方法参见http://workerman.net/gatewaydoc/gateway-worker-development/business-worker.html（BusinessWorker类的使用——业务处理类 Events）
回调方法必须是public类型的静态方法


在应用根目录增加入口文件 gateway.php

~~~
#!/usr/bin/env php
<?php
define('APP_PATH', __DIR__ . '/application/');

define('BIND_MODULE','gateway/Worker');

// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
~~~

在命令行启动服务端
~~~
php server.php start
~~~


linux下面可以支持下面指令
~~~
php gateway.php start|stop|status|restart|reload
~~~

在命令行中进行客户端测试
telnet 127.0.0.1 4399