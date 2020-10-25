<?php

/**
 * MongoDB支持类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Library
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=framework&type=license
 */

namespace sy\lib\db;
use \Sy;
//异常
use \Exception;
use \sy\base\SYException;
use \sy\base\SYDException;

class MongoDB {
	protected $dbtype = 'MongoDB';
	//MongoDB
	protected $WriteConcern = [
		'majority' => \MongoDB\Driver\WriteConcern::MAJORITY
	];
	protected $ReadConcern = [
		'majority' => \MongoDB\Driver\ReadConcern::MAJORITY,
		'local' => \MongoDB\Driver\ReadConcern::LOCAL
	];
	//连接信息
	protected $link = [];
	protected $dbInfo = [];
	protected $option = [];
	protected $writeConcern = [];
	//当前
	public $currentDB = [];
	public $currentCollection = [];
	public $lastError = [];
	protected $current;
	protected static $_instance = NULL;
	public static function i($id = 'default') {
		if (static::$_instance === NULL) {
			static::$_instance = new static($id);
		} else {
			static::$_instance->setCurrent($id);
		}
		return static::$_instance;
	}
	/**
	 * 设置当前Server
	 * @param string $current
	 */
	public function setCurrent($current) {
		if ($this->current === $current) {
			return;
		}
		$this->current = $current;
		$this->currentDB[$current] = NULL;
		$this->currentCollection[$current] = NULL;
	}
	/**
	 * 构造函数，自动连接
	 * @access public
	 */
	public function __construct($current) {
		if (!extension_loaded('mongodb')) {
			throw new SYException('Extension "mongodb" is required', '10026');
		}
		$this->setCurrent($current);
		if (Sy::$app->has('mongo') && $current === 'default') {
			$this->setParam(Sy::$app->get('mongo'));
		}
	}
	/**
	 * 连接到MongoDB
	 * @access protected
	 */
	protected function connect() {
		$id = $this->current;
		$dsn = 'mongodb://';
		//密码验证
		$authorize = '';
		if (isset($this->dbInfo[$id]['user']) && !empty($this->dbInfo[$id]['user'])) {
			$authorize = $this->dbInfo[$id]['user'] . ':' . $this->dbInfo[$id]['password'] . '@';
		}
		//多个服务器
		if (is_array($this->dbInfo[$id]['server'])) {
			foreach ($this->dbInfo[$id]['server'] as $server) {
				$dsn .= $authorize . $server['host'] . ':' . $server['port'] . ',';
			}
			$dsn = rtrim($dsn, ',');
		} else {
			$dsn .= $authorize . $this->dbInfo[$id]['host'] . ':' . $this->dbInfo[$id]['port'];
		}
		try {
			$this->link[$id] = new \MongoDB\Driver\Manager($dsn);
		} catch (\Exception $e) {
			throw new SYDException($e->getMessage(), $this->dbtype, '');
		}
		$this->setOption();
		if (isset($this->dbInfo[$id]['name'])) {
			$this->db($this->dbInfo[$id]['name']);
		}
	}
	/**
	 * 设置Server
	 * @access public
	 * @param array $param MongoDB服务器参数
	 */
	public function setParam($param) {
		$id = $this->current;
		$this->dbInfo[$id] = $param;
		$this->link[$id] = NULL;
		$this->connect();
	}
	/**
	 * 处理Key
	 * @access protected
	 * @param string $k
	 * @return string
	 */
	protected function setQuery($k) {
		$id = $this->current;
		return str_replace('#@__', $this->dbInfo[$id]['prefix'], $k);
	}
	/**
	 * 选择一个数据库
	 * @access public
	 * @param string $name 数据库名
	 * @return object(this)
	 */
	public function db($name = '') {
		$id = $this->current;
		if (empty($name) && isset($this->dbInfo[$id]['name'])) {
			$name = $this->dbInfo[$id]['name'];
		}
		if (empty($name)) {
			throw new SYDException('Unknow database', $this->dbtype, '');
		}
		$this->currentDB[$id] = $name;
		return $this;
	}
	/**
	 * 查询
	 * @access public
	 * @param string $collection 集合名
	 * @return object(this)
	 */
	public function select($collection) {
		$id = $this->current;
		if (empty($this->currentDB[$id])) {
			throw new SYDException('You must select a database', $this->dbtype, '');
		}
		$collection = $this->setQuery($collection);
		$this->currentCollection[$id] = $collection;
		return $this;
	}
	/**
	 * 执行高级查询
	 * @access public
	 * @param array $param 参数
	 * @return mixed
	 */
	public function executeCommand($param = []) {
		$id = $this->current;
		try {
			$param = new \MongoDB\Driver\Command($param);
			$cursor = $this->link[$id]->executeCommand($this->currentDB[$id], $param);
			$cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
		} catch (Exception $e) {
			$this->setError($e->getMessage(), 'executeCommand', $param);
			return FALSE;
		}
		return $cursor;
	}
	/**
	 * 错误相关：获取错误信息
	 * @access public
	 * @return array
	 */
	public function getLastError() {
		$id = $this->current;
		return $this->lastError[$id];
	}
	protected function setError($message, $method, $param) {
		$id = $this->current;
		$this->lastError[$id] = [
			'message' => $message,
			'method' => $method,
			'param' => $param
		];
	}
	/**
	 * 执行读操作
	 * @access public
	 * @param array $filter
	 * @return array
	 */
	public function get($filter, $option = []) {
		$id = $this->current;
		try {
			$query = new \MongoDB\Driver\Query($filter, $option);
			$cursor = $this->link[$id]->executeQuery($this->getNamespace(), $query);
			$cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
		} catch (\Exception $e) {
			$this->setError($e->getMessage(), 'GET', [$filter, $option]);
			return FALSE;
		}
		return $cursor->toArray();
	}
	public function getOne($filter, $option = []) {
		$option['limit'] = 1;
		$r = $this->get($filter, $option);
		if (is_array($r)) {
			return $r[0];
		} else {
			return FALSE;
		}
	}
	/**
	 * 删除记录
	 * @access public
	 * @param array $filter
	 * @return object
	 */
	public function delete($filter, $justOne = FALSE) {
		$id = $this->current;
		try {
			$bulk = new \MongoDB\Driver\BulkWrite;
			if ($justOne) {
				$bulk->delete($filter, ['limit' => 1]);
			} else {
				$bulk->delete($filter, ['limit' => 0]);
			}
			$result = $this->link[$id]->executeBulkWrite($this->getNamespace(), $bulk, $this->writeConcern[$id]);
		} catch (\Exception $e) {
			$this->setError($e->getMessage(), 'DELETE', $filter);
			return FALSE;
		}
		return $result;
	}
	/**
	 * 更新记录
	 * @access public
	 * @param array $filter 查询条件
	 * @param array $to 设置为
	 * @param boolean $justOne 是否仅更新一条记录
	 * @param boolean $autoInsert 在没有匹配记录时，是否自动插入
	 * @return object
	 */
	public function update($filter, $to, $justOne = FALSE, $autoInsert = FALSE) {
		$id = $this->current;
		try {
			$bulk = new \MongoDB\Driver\BulkWrite;
			$bulk->update(
				$filter,
				['$set' => $to],
				['multi' => !$justOne, 'upsert' => $autoInsert]
			);
			$result = $this->link[$id]->executeBulkWrite($this->getNamespace(), $bulk, $this->writeConcern[$id]);
		} catch (\Exception $e) {
			$this->setError($e->getMessage(), 'UPDATE', [$filter, $to]);
			return FALSE;
		}
		return $result;
	}
	/**
	 * 增加记录
	 * @access public
	 * @param array $data
	 * @return array
	 */
	public function insert($data) {
		$id = $this->current;
		try {
			$bulk = new \MongoDB\Driver\BulkWrite;
			$oid = $bulk->insert($data);
			if (empty($oid)) {
				$oid = $data['_id'];
			}
			$result = $this->link[$id]->executeBulkWrite($this->getNamespace(), $bulk, $this->writeConcern[$id]);
		} catch (\Exception $e) {
			$this->setError($e->getMessage(), 'INSERT', $data);
			return FALSE;
		}
		return [strval($oid), $result];
	}
	/**
	 * 连接DB和Collection
	 * @access protected
	 * @return string
	 */
	protected function getNamespace() {
		$id = $this->current;
		return $this->currentDB[$id] . '.' . $this->currentCollection[$id];
	}
	/**
	 * 设置选项
	 * @access public
	 * @param array $option
	 * @return object
	 */
	public function setOption($option = []) {
		$id = $this->current;
		$default = [
			'timeout' => 3000,
			'safe' => FALSE
		];
		$this->option[$id] = array_merge($default, $option);
		$this->writeConcern[$id] = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, $this->option[$id]['timeout'], $this->option[$id]['safe']);
		return $this;
	}
	/**
	 * 静态方法：实例化ID
	 */
	public static function MongoID($id) {
		try {
			$id = new \MongoDB\BSON\ObjectID($id);
			return $id;
		} catch (\Exception $e) {
			return FALSE;
		}
	}
}
