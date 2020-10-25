<?php
// ModPHP 压缩包名称，如果设置，ModPHP 将从 ZIP 中加载内核
defined('MOD_ZIP') or define('MOD_ZIP', '');

if(PHP_SAPI != 'cli')
	exit("Multi-threading socket server must run in command line.");
elseif(!class_exists('Thread'))
	exit("PHP does not support multi-threading yet.");
elseif(!extension_loaded('sockets'))
	exit("Extension 'sockets' is not loaded, cannot start socket server.");

/** 创建线程类 */
class SocketServerThread extends Thread{
	/** 将服务器资源传入线程作用域 */
	function __construct($server){
		$this->server = $server;
	}
	function run(){
		SocketServer::server($this->server); //设置服务器
		include (MOD_ZIP ? 'zip://'.__DIR__.'/'.MOD_ZIP.'#' : '').'socket-server.php'; //引入 SocketServer 服务
		SocketServer::start(); //开启服务
	}
}

include (MOD_ZIP ? 'zip://'.__DIR__.'/'.MOD_ZIP.'#' : '').'mod/classes/socketserver.class.php'; //引入 SocketServer 扩展

/** 监听端口 */
$port = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 8080;
$server = SocketServer::listen($port, function($server, $port){
	$tip = "SocketServer $server started on $port at ".date('D M d H:i:s Y');
	fwrite(STDOUT, $tip.PHP_EOL.PHP_EOL);
}, false); //将第三个参数($autoStart)设置为 false

$threads = array(); //线程组

/** 创建若干个线程并加入线程组 */
for ($i=0; $i < (isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : 5); $i++) {
	$threads[$i] = new SocketServerThread($server);
	$threads[$i]->start();
}

/** 引入交互式控制台，可以监控线程组 */
require_once (MOD_ZIP ? 'zip://'.__DIR__.'/'.MOD_ZIP.'#' : '').'mod.php'; //引入初始化程序