<?php
/**
 * 连接池基本类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Library
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Connection;

use SplQueue;
use Swoole\Coroutine as co;
use Yesf\Swoole;
use Yesf\Connection\Pool;
use Yesf\Exception\NotFoundException;

trait PoolTrait {
	/** @var SplQueue $connection All connections */
	protected $connection = null;

	/** @var int $connection_count Current connected count */
	protected $connection_count = 0;

	/** @var int $last_run_out_time Last time when use out all connections */
	protected $last_run_out_time = null;

	/** @var SplQueue $wait Waiting coroutine queue */
	protected $wait = null;

	/** @var int $min_client Min count */
	protected $min_client = PHP_INT_MAX;

	/** @var int $max_client Max count */
	protected $max_client = PHP_INT_MAX;

	/**
	 * Setup connection pool
	 * 
	 * @access public
	 * @param array $config
	 */
	public function initPool($config) {
		if (!method_exists($this, 'getMinClient') || !method_exists($this, 'getMaxClient')) {
			throw new NotFoundException("Method getMinClient or getMaxClient not found");
		}
		$this->wait = new SplQueue;
		$this->connection = new SplQueue;
		$this->last_run_out_time = time();
		if (isset($config['min'])) {
			$this->min_client = intval($config['min']);
		}
		if (isset($config['max'])) {
			$this->max_client = intval($config['max']);
		}
		//建立最小连接
		$count = $this->getMinClient();
		while ($count--) {
			$this->createConnection();
		}
	}
	protected function getMinClient() {
		return $this->min_client === PHP_INT_MAX ? Pool::getMin() : $this->min_client;
	}
	protected function getMaxClient() {
		return $this->max_client === PHP_INT_MAX ? Pool::getMax() : $this->max_client;
	}
	/**
	 * 获取一个可用连接
	 * 如果不存在可用连接，会自动判断是否需要建立新的连接
	 * 
	 * @access public
	 * @return object
	 */
	public function getConnection() {
		if ($this->connection->count() === 0) {
			//是否需要建立新的连接
			if ($this->getMaxClient() > $this->connection_count) {
				$this->last_run_out_time = time();
				return $this->connect();
			}
			//wait
			$uid = co::getUid();
			$this->wait->push($uid);
			co::suspend();
			return $this->connection->pop();
		}
		if ($this->connection->count() === 1) {
			$this->last_run_out_time = time();
		}
		return $this->connection->pop();
	}
	/**
	 * 使用完成连接，归还给连接池
	 * 
	 * @access public
	 * @param object $connection
	 */
	public function freeConnection($connection) {
		$this->connection->push($connection);
		if ($this->wait->count() > 0) {
			$uid = $this->wait->pop();
			co::resume($uid);
		} else {
			//有连接处于空闲状态超过15秒，关闭一个连接
			if ($this->connection_count > $this->getMinClient() && time() - $this->last_run_out_time > 15) {
				$this->close();
			}
		}
	}
	/**
	 * 断开一个连接
	 * 
	 * @access public
	 */
	public function close() {
		$this->connection_count--;
	}
	/**
	 * 创建新的连接，并压入连接池
	 * 
	 * @access protected
	 */
	protected function createConnection() {
		$this->connection->push($this->connect());
		$this->connection_count++;
	}
	/**
	 * 创建新的连接并返回
	 * 
	 * @access protected
	 */
	protected function connect() {
	}
	/**
	 * Reconnect
	 * 
	 * @access public
	 * @param $connection
	 */
	public function reconnect($connection) {
		$this->close();
		return $this->connect();
	}
}