<?php
/**
 * PDO_SQLite支持类
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

class Sqlite extends PDOAbstract {
	/**
	 * 连接到MySQL
	 * @access protected
	 */
	protected function connect() {
		$this->config = App::$config->get('mysql');
		//对老版本的支持
		if ($this->config['version'] === 'sqlite3') {
			$dsn = 'sqlite:';
		} else {
			$dsn = 'sqlite2:';
		}
		$path = str_replace('@app/', APP_PATH, $this->config['path']);
		$dsn .= $path;
		try {
			$this->connection = new \PDO($dsn);
		} catch (\PDOException $e) {
			throw new DBException($e->getMessage());
		}
	}
	/**
	 * 查询并返回一条结果
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
		return new QueryFactory();
	}
}
