<?php
/**
 * PDO_MySQL支持类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Library
 * @link https://www.sylibs.com/
 * @copyright Copyright (c) 2015-2019 ShuangYa
 * @license https://syframework.sylibs.com/license.html
 */
namespace Sy\DB;

use Sy\App;
use Sy\Exception\DBException;
use Latitude\QueryBuilder\QueryFactory;

class Mysql extends PDOAbstract implements DBInterface {
	/**
	 * 连接到MySQL
	 * @access protected
	 */
	protected function connect() {
		$this->config = App::$config->get('mysql');
		$dsn = 'mysql:host=' . $this->config['host'] . ';port=' . $this->config['port'] . ';';
		if (isset($this->config['database'])) {
			$dsn .= 'dbname=' . $this->config['database'] . ';';
		}
		$dsn .= 'charset=' . strtolower(str_replace('-', '', App::$config->get('charset')));
		try {
			$this->connection = new \PDO($dsn, $this->config['user'], $this->config['password']);
		} catch (\PDOException $e) {
			throw new DBException($e->getMessage());
		}
	}
	/**
	 * 查询并返回一条结果
	 * 
	 * @access public
	 * @param string $sql SQL语句
	 * @param array $data 参数
	 * @return array
	 */
	public function get(string $sql, $data = null) {
		if (stripos($sql, 'limit') === false) {
			$sql .= ' LIMIT 0,1';
		}
		$r = $this->query($sql, $data);
		return current($r);
	}
	/**
	 * 获取Builder对象
	 * 
	 * @access public
	 * @return object
	 */
	public static function getBuilder() {
		return new QueryFactory('mysql');
	}
}
