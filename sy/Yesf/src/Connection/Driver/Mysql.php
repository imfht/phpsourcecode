<?php
/**
 * MySQL封装类
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

class Mysql implements PoolInterface {
	use PoolTrait;
	protected $config = null;
	public function __construct(array $config) {
		$this->config = $config;
		$this->initPool($config);
	}
	/**
	 * 根据配置连接到数据库
	 * 
	 * @access protected
	 */
	protected function connect() {
		$connection = new co\MySQL();
		return $this->reconnect($connection);
	}
	public function reconnect($connection) {
		$r = $connection->connect([
			'host' => $this->config['host'],
			'user' => $this->config['user'],
			'password' => $this->config['password'],
			'database' => $this->config['database'],
			'port' => $this->config['port'],
			'timeout' => 3,
			'charset' => 'utf8'
		]);
		if ($r === false) {
			throw new ConnectionException('Can not connect to database server');
		}
		return $connection;
	}
}
