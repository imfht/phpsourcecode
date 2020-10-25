<?php
/**
 * Redis封装类
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
use Yesf\Exception\Exception;
use Yesf\Exception\ConnectionException;
use Swoole\Coroutine as co;

class Redis implements PoolInterface {
	use PoolTrait;
	private $options = [];
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
		$connection = new co\Redis();
		$r = $connection->connect($this->config['host'], $this->config['port']);
		if ($r === false) {
			throw new DBException('Can not connect to database server, ' . $connection->errMsg, $connection->errCode);
		}
		if (!empty($this->config['password'])) {
			$r = $connection->auth($this->config['password']);
			if ($r === false) {
				throw new ConnectionException('Authenticate failed, ' . $connection->errMsg, $connection->errCode);
			}
		}
		if (isset($this->config['index'])) {
			$r = $connection->select(intval($this->config['index']));
			if ($r === false) {
				throw new ConnectionException('Select database failed, ' . $connection->errMsg, $connection->errCode);
			}
		}
		foreach ($this->options as $k => $v) {
			$connection->setOption($k, $v);
		}
		return $connection;
	}
	/**
	 * 魔术方法，调用随机连接
	 * 
	 * @access public
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		$connection = $this->getConnection();
		if (!method_exists($connection, $name)) {
			$this->freeConnection($connection);
			throw new Exception('Method ' . $name . ' not exists');
		}
		$tryAgain = true;
REDIS_START_EXECUTE:
		$result = $connection->$name(...$arguments);
		//发生了错误
		if ($connection->errCode !== 0) {
			if (!$connection->connected && $tryAgain) {
				@$connection->close();
				$tryAgain = false;
				$connection = $this->reconnect($connection);
				goto REDIS_START_EXECUTE;
			} else {
				$error = $connection->errMsg;
				$errno = $connection->errCode;
				$this->freeConnection($connection);
				throw new DBException($error, $errno);
			}
		}
		$this->freeConnection($connection);
		return $result;
	}
	/**
	 * setOption封装
	 * 会对所有连接执行，并压入一个数组，每次新建连接会自动执行
	 * 此方法的执行会阻塞所有连接，直到所有连接都执行完成
	 * 
	 * @access public
	 * @param int $name
	 * @param mixed $value
	 */
	public function setOption($name, $value) {
		$all = [];
		for ($i = 1; $i <= $this->connection_count; $i++) {
			$connection = $this->getConnection();
			$connection->setOption($name, $value);
			$all[] = $connection;
		}
		foreach ($all as $v) {
			$this->freeConnection($v);
		}
	}
}