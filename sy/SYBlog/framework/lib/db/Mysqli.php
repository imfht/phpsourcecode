<?php

/**
 * MySQLi支持类
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
use \sy\lib\Html;
use \sy\base\SYException;
use \sy\base\SYDException;

class Mysqli {
	protected $dbtype = 'MySQL';
	protected $link = [];
	protected $dbInfo = [];
	protected $result = [];
	protected static $_instance = NULL;
	protected static $current = 'default';
	public static function i($id = 'default') {
		if (static::$_instance === NULL) {
			static::$_instance = new static;
		}
		static::$_instance->current = $id;
		return static::$_instance;
	}
	/**
	 * 构造函数，自动连接
	 * @access public
	 */
	public function __construct() {
		if (!class_exists('mysqli', FALSE)) {
			throw new SYException('Class "MySQLi" is required', '10020');
		}
		if (Sy::$app->has('mysql') && $this->current === 'default') {
			$this->setParam(Sy::$app->get('mysql'));
		}
	}
	/**
	 * 设置Server
	 * @access public
	 * @param array $param MySQL服务器参数
	 */
	public function setParam($param) {
		$id = $this->current;
		$this->dbInfo[$id] = $param;
		$this->link[$id] = NULL;
		$this->connect();
	}
	/**
	 * 连接到MySQL
	 * @access protected
	 */
	protected function connect() {
		$id = $this->current;
		$config = $this->dbInfo[$id];
		$this->link[$id] = new \mysqli($config['host'], $config['user'], $config['password'], $config['name'], $config['port']);
		if ($this->link[$id]->connect_error) {
			throw new SYDException(YHtml::encode($this->link[$id]->connect_error), $this->dbtype, 'NULL');
		}
		$this->link[$id]->set_charset(strtolower(str_replace('-', '', Sy::$app->get('charset'))));
	}
	/**
	 * 处理Key
	 * @access protected
	 * @param string $sql
	 * @return string
	 */
	protected function setQuery($sql) {
		$id = $this->current;
		return str_replace('#@__', $this->dbInfo[$id]['prefix'], $sql);
	}
	/**
	 * 获取最后产生的ID
	 * @access public
	 * @return int
	 */
	public function getLastId() {
		$id = $this->current;
		return intval($this->link[$id]->insert_id);
	}
	/**
	 * 执行查询
	 * @access public
	 * @param string $key
	 * @param string $sql SQL语句
	 * @param array $data 参数
	 */
	public function query($sql, $data = NULL) {
		$id = $this->current;
		$sql = $this->setQuery($sql);
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$v = addslashes($v);
				$k = is_int($k) ? '?' : ':' . $k;
				$sql = str_replace($k, "'$v'", $sql, 1);
			}
		}
		$rs = $this->link[$id]->query($sql);
		//执行失败
		if ($rs === FALSE) {
			throw new SYDException(YHtml::encode($this->link[$id]->error), $this->dbtype, YHtml::encode($sql));
		}
		if (is_object($rs)) {
			$r = $rs->fetch_all(MYSQLI_ASSOC);
			$rs->free();
			return $r;
		} else {
			return NULL;
		}
	}
	/**
	 * 查询并返回一条结果
	 * @access public
	 * @param string $sql SQL语句
	 * @param array $data 参数
	 * @return array
	 */
	public function getOne($sql, $data = NULL) {
		if (!preg_match('/limit ([0-9,]+)$/', strtolower($sql))) {
			$sql .= ' LIMIT 0,1';
		}
		$r = $this->query($sql, $data);
		return current($r);
	}
	/**
	 * 事务：开始
	 * @access public
	 */
	public function beginTransaction() {
		$id = $this->current;
		$this->link[$id]->autocommit(FALSE);
	}
	/**
	 * 事务：添加一句
	 * @access public
	 * @param string $sql
	 */
	public function addOne($sql) {
		$id = $this->current;
		$this->link[$id]->query($this->setQuery($sql, $id));
	}
	/**
	 * 事务：提交
	 * @access public
	 */
	public function commit() {
		$id = $this->current;
		$this->link[$id]->commit();
	}

	/**
	 * 事务：回滚
	 * @access public
	 */
	public function rollback() {
		$id = $this->current;
		$this->link[$id]->rollback();
	}
	/**
	 * 析构函数，自动关闭
	 * @access public
	 */
	public function __destruct() {
		foreach ($this->link as $link) {
			if (is_object($link) && method_exists($link, 'close')) {
				@$link->close();
			}
		}
	}
}
