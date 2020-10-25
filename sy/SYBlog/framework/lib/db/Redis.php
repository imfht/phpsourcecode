<?php

/**
 * Redis支持类
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
use \Redis as SRedis;
use \sy\base\SYException;
use \sy\base\SYDException;

class Redis {
	protected $dbtype = 'Redis';
	protected $link = [];
	protected $dbInfo = [];
	protected $transaction = [];
	protected $result = [];
	protected $current = '';
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
	}
	/**
	 * 构造函数，自动连接
	 * @access public
	 */
	public function __construct($current) {
		if (!class_exists('SRedis', FALSE)) {
			throw new SYException('Class "Redis" is required', '10022');
		}
		$this->setCurrent($current);
		if (Sy::$app->has('redis') && $current === 'default') {
			$this->setParam(Sy::$app->get('redis'));
		}
	}
	/**
	 * 连接到Redis
	 * @access protected
	 */
	protected function connect() {
		$id = $this->current;
		$this->link[$id] = new SRedis;
		$this->link[$id]->connect($this->dbInfo[$id]['host'], $this->dbInfo['port']);
		if (!empty($this->dbInfo[$id]['password'])) {
			$this->link[$id]->auth($this->dbInfo['password']);
		}
	}
	/**
	 * 设置Server
	 * @access public
	 * @param array $param Redis服务器参数
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
	 * 使用魔术方法，调用phpredis的方法
	 */
	public function __call($name, $args) {
		$id = $this->current;
		if (!method_exists($this->link[$id], $name)) {
			throw new SYDException("Method '$name' not exists", $this->dbtype);
		}
		$name_lower = strtolower($name);
		if (in_array($name_lower, ['mget', 'getmultiple', 'sdiff', 'sinter', 'sunion'], TRUE)) {
			//均为Key的，如mGet
			foreach ($args as $k => $v) {
				$args[$k] = $this->setQuery($v);
			}
		} elseif (!in_array($name_lower, ['get', 'set', 'mset', 'msetnx', 'migrate', 'sdiffstore', 'sinterstore', 'smove', 'rename', 'renamekey', 'renamenx'], TRUE)) { //不属于特殊处理的
			$args[0] = $this->setQuery($args[0]);
		} else { //特殊处理
			switch ($name_lower) {
				case 'mset':
				case 'msetnx':
					$keys = $args[0];
					$new_keys = [];
					foreach ($keys as $k => $v) {
						$new_k = $this->setQuery($k);
						$new_keys[$new_k] = $v;
					}
					unset($keys);
					$args[0] = $new_keys;
					break;
				case 'migrate':
					$args[2] = $this->setQuery($args[2]);
					break;
				case 'sdiffstore':
				case 'sinterstore':
				case 'sunionstore':
					foreach ($args as $k => $v) {
						if ($k === 0) {
							continue;
						}
						$args[$k] = $this->setQuery($v);
					}
					break;
				case 'rename':
				case 'renamekey':
				case 'renamenx':
				case 'smove':
					$args[0] = $this->setQuery($args[0]);
					$args[1] = $this->setQuery($args[1]);
					break;
			}
		}
		//对事务的支持
		if ($this->transaction[$id] === NULL) {
			$r = call_user_func_array([$this->link[$id], $name], $args);
			return $r;
		} else {
			$this->transaction[$id] = call_user_func_array([$this->transaction[$id], $name], $args);
		}
	}
	/**
	 * 事务：开始
	 * @access public
	 */
	public function beginTransaction() {
		$id = $this->current;
		$this->transaction[$id] = $this->link[$id]->mulit();
	}
	/**
	 * 事务：提交
	 * @access public
	 */
	public function commit() {
		$id = $this->current;
		$r = $this->transaction[$id]->exec();
		$this->transaction[$id] = NULL;
		return $r;
	}
	/**
	 * 析构函数，自动关闭
	 * @access public
	 */
	public function __destruct() {
		foreach ($this->link as $link) {
			if (method_exists($link, 'close')) {
				@$link->close();
			}
		}
	}
}
