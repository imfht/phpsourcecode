<?php
/**
 * TCP封装类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Driver
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Connection\Driver;

use Yesf\Yesf;
use Yesf\Connection\PoolTrait;
use Yesf\Connection\PoolInterface;
use Yesf\Exception\ConnectionException;
use Swoole\Coroutine as co;

class TcpClient implements PoolInterface {
	use PoolTrait;
	protected $config = null;
	public function __construct(array $config) {
		$this->config = $config;
		$this->initPool($config);
	}
	protected function connect() {
		$connection = new co\Client(SWOOLE_SOCK_TCP);
		return $this->reconnect($connection);
	}
	public function reconnect($connection) {
		$r = $connection->connect($this->config['host'], $this->config['port'], 3);
		if ($r === false) {
			throw new ConnectionException(sprintf('Can not connect to %s:%s', $this->config['host'], $this->config['port']));
		}
		return $connection;
	}
}