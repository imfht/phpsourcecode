<?php
/**
 * Swoole主要操作类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Swoole
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf;

use Swoole\Http\Server as SwooleServer;
use Yesf\Yesf;
use Yesf\Event\Server;
use Yesf\Event\Listener;
use Yesf\Event\HttpServer;
use Yesf\Exception\NotFoundException;

class Swoole {
	const LISTEN_TCP = SWOOLE_TCP;
	const LISTEN_UDP = SWOOLE_UDP;
	const LISTEN_UNIX = SWOOLE_UNIX_STREAM;
	const LISTEN_UNIX_DGRAM = SWOOLE_UNIX_DGRAM;
	const LISTEN_TCP6 = SWOOLE_TCP6;
	const LISTEN_UDP6 = SWOOLE_UDP6;
	//当前是否为task进程，在workerStart后才有效
	public static $isTaskWorker = false;
	//Swoole实例类
	protected static $server = null;
	/**
	 * 初始化
	 * 
	 * @access public
	 */
	public static function init() {
		self::$server = new SwooleServer(Yesf::app()->getConfig('ip', Yesf::CONF_SERVER), Yesf::app()->getConfig('port', Yesf::CONF_SERVER)); 
		//基本配置
		$config = Yesf::app()->getConfig('advanced', Yesf::CONF_SERVER);
		$ssl = Yesf::app()->getConfig('ssl', Yesf::CONF_SERVER);
		if ($ssl && $ssl['enable']) {
			$config['ssl_cert_file'] = $ssl['cert'];
			$config['ssl_key_file'] = $ssl['key'];
		}
		if (Yesf::app()->getConfig('http2', Yesf::CONF_SERVER)) {
			$config['open_http2_protocol'] = true;
		}
		self::$server->set($config);
		//基本事件
		self::$server->on('Start', [Server::class, 'onStart']);
		self::$server->on('Shutdown', [Server::class, 'onShutdown']);
		self::$server->on('ManagerStart', [Server::class, 'onManagerStart']);
		self::$server->on('ManagerStop', [Server::class, 'onManagerStop']);
		self::$server->on('WorkerStart', [Server::class, 'onWorkerStart']);
		self::$server->on('WorkerError', [Server::class, 'onWorkerError']);
		self::$server->on('Task', [Server::class, 'onTask']);
		self::$server->on('Finish', [Server::class, 'onFinish']);
		self::$server->on('PipeMessage', [Server::class, 'onPipeMessage']);
		//HTTP事件
		self::$server->on('Request', [HttpServer::class, 'onRequest']);
	}
	public static function start() {
		self::$server->start();
	}
	/**
	 * 获取统计数据
	 * 
	 * @access public
	 * @return array
	 */
	public static function getStat() {
		return self::$server->stats();
	}
	/**
	 * 重载
	 * 
	 * @access public
	 * @param bool $task 是否重载Task进程
	 */
	public static function reload($task = true) {
		self::$server->reload($task);
	}
	/**
	 * 添加监听
	 * 
	 * @access public
	 * @param int $type 监听类型
	 * @param mixed $config 选项，可以为数组或配置项名称
	 * @return bool
	 */
	public static function listen(int $type, $config) {
		if (is_string($config)) {
			$config = Yesf::app()->getConfig($config, Yesf::CONF_SERVER);
		}
		return new Listener($type, $config);
	}
	/**
	 * 投递Task
	 * 
	 * @access public
	 * @param mixed $data 传递数据
	 * @param int $worker_id 投递到的task进程ID
	 * @param callable $callback 回调函数
	 */
	public static function task($data, $worker_id = -1, $callback = null) {
		if ($callback === true) {
			return self::$server->taskCo([$data]);
		} elseif (is_callable($callback)) {
			self::$server->task($data, $worker_id, $callback);
		} else {
			self::$server->task($data, $worker_id);
		}
	}
	/**
	 * 批量投递Task
	 * 对于不同的$callback，有如下三种处理方式：
	 * $callback为TRUE：使用协程方式等待
	 * $callback为回调函数：使用异步投递，并等待返回
	 * $callback为空：异步投递
	 * 
	 * @access public
	 * @param array $data 传递数据
	 * @param bool/callable $callback 回调函数
	 */
	public static function taskMulti($data, $callback) {
		if ($callback === true) {
			return self::$server->taskCo($data);
		} elseif (is_callable($callback)) {
			$result = [];
			$ids = [];
			foreach ($data as $k => $v) {
				$task_id = self::$server->task($v, -1, function($serv, $id, $res) use (&$data, &$result, &$callback, &$ids) {
					$result[$ids[$id]] = $res;
					if (count($result) === count($data)) {
						$callback($data);
					}
				});
				$ids[$task_id] = $k;
			}
		} else {
			foreach ($data as $k => $v) {
				self::$server->task($v, -1);
			}
		}
	}
	/**
	 * 向客户端发送消息
	 * 
	 * @access public
	 * @param string $data
	 * @param int $fd
	 * @param int $from_id
	 */
	public static function send(string $data, $fd, $from_id = 0) {
		self::$server->send($fd, $data, $from_id);
	}
	/**
	 * 向UDP客户端发送消息
	 * 
	 * @access public
	 * @param string $data
	 * @param mixed $addr
	 * @param int $port
	 */
	public static function sendToUDP(string $data, $addr, $port = 0, $from = -1) {
		self::$server->sendto($addr, $port, $data);
	}
	/**
	 * 发送消息到某个worker进程（支持task_worker）
	 * 
	 * @access public
	 * @param string $message
	 * @param int $worker_id
	 */
	public static function sendToWorker($message, $worker_id) {
		self::$server->sendMessage($message, $worker_id);
	}
	/**
	 * 获取Swoole示例，用于实现更多高级操作
	 * 
	 * @access public
	 * @return object(\Swoole\Server)
	 */
	public static function getSwoole() {
		return self::$server;
	}
}