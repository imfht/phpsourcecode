<?php
/**
 * Listener
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Swoole
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Event;

use Yesf\Yesf;
use Yesf\Swoole as YesfSwoole;
use Yesf\Exception\ListenException;

class Listener {
	/** @var array $listened */
	private static $listened = [];
	public $type;
	public $ip;
	public $port;
	public $addr;
	public $config;
	/** @var Swoole\Server\Port $listener */
	public $listener;
	/** @var array $callback */
	private $callback = [];
	/**
	 * Constructor
	 * 
	 * @access public
	 * @param int $type
	 * @param array $config
	 */
	public function __construct(int $type, $config) {
		//If type is unix, do not need port
		if ($type === YesfSwoole::LISTEN_UNIX || $type === YesfSwoole::LISTEN_UNIX_DGRAM) {
			$addr = $config['sock'];
			$port = 0;
			if (empty($addr)) {
				throw new ListenException("Listen failed: socket address can not be empty");
			}
			if (isset(self::$listened[$addr])) {
				throw new ListenException("Listen failed: $addr is already listened");
			}
			$this->addr = $addr;
		} else {
			$addr = isset($config['ip']) ? $config['ip'] : Yesf::app()->getConfig('ip', Yesf::CONF_SERVER);
			if (!isset($config['port'])) {
				throw new ListenException("Listen failed: port can not be empty");
			}
			$port = $config['port'];
			if (isset(self::$listened[$port])) {
				throw new ListenException("Listen failed: port $port is already listened");
			}
			$this->ip = $addr;
			$this->port = $port;
		}
		$this->type = $type;
		/** @var Swoole\Server\Port $service */
		$service = YesfSwoole::getSwoole()->addListener($addr, $port, $type);
		$this->config = $config;
		$this->listener = $service;
		self::$listened[$addr] = $this;
		switch ($type) {
			// TCP
			case YesfSwoole::LISTEN_TCP:
			case YesfSwoole::LISTEN_TCP6:
			case YesfSwoole::LISTEN_UNIX:
				if (!isset($this->config['open_http_protocol']) || $this->config['open_http_protocol'] !== false) {
					$service->on('Receive', [$this, 'onReceive']);
					$service->on('Connect', [$this, 'onConnect']);
					$service->on('Close', [$this, 'onClose']);
				} elseif ($this->config['open_websocket_protocol']) {
					$service->on('Message', [$this, 'onMessage']);
					$service->on('Open', [$this, 'onOpen']);
				} else {
					$service->on('Receive', [$this, 'onRequest']);
				}
				break;
			//Unix或TCP
			case YesfSwoole::LISTEN_UDP:
			case YesfSwoole::LISTEN_UDP6:
			case YesfSwoole::LISTEN_UNIX_DGRAM:
				$service->on('Packet', [$this, 'onPacket']);
				break;
		}
	}
	/**
	 * Destructor
	 * 
	 * @access public
	 */
	public function close() {
		if ($this->type === YesfSwoole::LISTEN_UNIX || $this->type === YesfSwoole::LISTEN_UNIX_DGRAM) {
			$key = $this->config['sock'];
		} else {
			$key = $this->config['port'];
		}
		$this->listener = null;
		unset(self::$listened[$key]);
	}
	public function __destruct() {
		$this->close();
	}
	/**
	 * TCP: Receive
	 * 
	 * @access public
	 * @param Server $server
	 * @param int $fd
	 * @param int $from_id
	 * @param string $data
	 */
	public function onReceive($server, $fd, $from_id, $data) {
		$this->handle(__METHOD__, [$fd, $from_id, $data]);
	}
	/**
	 * TCP: Connect
	 * 
	 * @access public
	 * @param Server $server
	 * @param int $fd
	 * @param int $from_id
	 */
	public function onConnect($server, $fd, $from_id) {
		$this->handle(__METHOD__, [$fd, $from_id]);
	}
	/**
	 * TCP: Close
	 * 
	 * @access public
	 * @param Server $server
	 * @param int $fd
	 * @param int $from_id
	 */
	public function onClose($server, $fd, $from_id) {
		$this->handle(__METHOD__, [$fd, $from_id]);
	}
	/**
	 * UDP: Packet
	 * 
	 * @access public
	 * @param Server $server
	 * @param string $data
	 * @param array $client_info
	 */
	public function onPacket($server, $data, $client_info) {
		$this->handle(__METHOD__, [$data, $client_info]);
	}
	/**
	 * Http: Request
	 * 
	 * @access public
	 * @param object $request
	 * @param object $response
	 */
	public function onRequest($request, $response) {
		$this->handle(__METHOD__, [$request, $response]);
	}
	/**
	 * WebSocket: Open
	 * 
	 * @access public
	 * @param Server $server
	 * @param object $request
	 */
	public function onOpen($server, $request) {
		$this->handle(__METHOD__, [$request]);
	}
	/**
	 * WebSocket: Message
	 * 
	 * @access public
	 * @param Server $server
	 * @param object $frame
	 */
	public function onMessage($server, $frame) {
		$this->handle(__METHOD__, [$frame]);
	}
	/**
	 * Set event handler
	 * 
	 * @access public
	 * @param string $event
	 * @param callable $callback
	 */
	public function on($event, $callback) {
		if (!is_callable($callback)) {
			throw new ListenException(var_export($callback, true) . " is not callback");
		}
		$this->callback[$event] = $callback;
	}
	/**
	 * Handle event
	 * 
	 * @access public
	 * @param string $event
	 * @param array $args
	 */
	public function handle($event, $args) {
		if (isset($this->callback[$event])) {
			$this->callback[$event](...$args);
		}
	}
}