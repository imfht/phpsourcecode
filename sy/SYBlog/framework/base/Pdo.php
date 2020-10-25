<?php

/**
 * PDO基本类
 * 注意：此为抽象类，无法被实例化
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=frameworkr&type=license
 */

namespace sy\base;
use \Sy;
use \PDOException;
use \sy\base\SYException;
use \sy\base\SYDException;
use \sy\lib\Html;

abstract class Pdo {
	protected $link = [];
	protected $dbInfo = [];
	protected $result = [];
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
		$this->current = $current;
	}
	/**
	 * 抽象函数：自动连接
	 * @access protected
	 */
	abstract protected function autoConnect();
	/**
	 * 抽象函数：连接
	 * @access protected
	 * @param string $id
	 */
	abstract protected function connect();
	/**
	 * 抽象函数：获取一个结果
	 * @access public
	 * @param string $sql
	 * @param array $data
	 * @param string $id 连接ID
	 * @return array
	 */
	abstract public function getOne($sql, $data);
	/**
	 * 构造函数，自动连接
	 * @access public
	 */
	public function __construct($current) {
		if (!class_exists('PDO', FALSE)) {
			throw new SYException('Class "PDO" is required', '10021');
		}
		$this->setCurrent($current);
		if ($current === 'default') {
			$this->autoConnect();
		}
	}
	/**
	 * 设置Server
	 * @access public
	 * @param array $param MySQL服务器参数
	 * @param string $id
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
	 * @param string $id
	 * @return int
	 */
	public function getLastId() {
		$id = $this->current;
		return intval($this->link[$id]->lastInsertId());
	}
	/**
	 * 执行查询
	 * @access public
	 * @param string $sql SQL语句
	 * @param array $data 参数
	 * @return array
	 */
	public function query($sql, $data = NULL) {
		$id = $this->current;
		$prepare_sql = $this->setQuery($sql);
		$st = $this->link[$id]->prepare($prepare_sql);
		if ($st === FALSE) {
			$e = $this->link[$id]->errorInfo();
			throw new SYDException(Html::encode($e[2]), $this->dbtype, $sql);
		}
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				if (is_numeric($k)) {
					$st->bindValue($k + 1, $v);
				} else {
					$st->bindValue($k, $v);
				}
			}
		}
		try {
			$r = $st->execute();
			if ($r === FALSE) {
				$e = $st->errorInfo();
				throw new SYDException(Html::encode($e[2]), $this->dbtype, $sql);
			}
		} catch (\PDOException $e) {
			throw new SYDException(Html::encode($e->getMessage()), $this->dbtype, $sql);
		}
		$st->setFetchMode(\PDO::FETCH_ASSOC);
		return $st->fetchAll();
	}
	/**
	 * Ping模拟
	 * @access public
	 * @return boolean
	 */
	public function ping() {
		$id = $this->current;
		try{
			$this->link[$id]->getAttribute(\PDO::ATTR_SERVER_INFO);
		} catch (\PDOException $e) {
			if (strpos($e->getMessage(), 'MySQL server has gone away') !== FALSE) {
				return false;
			}
		}
		return true;
	}
}