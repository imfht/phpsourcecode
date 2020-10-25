<?php
/**
 * PDO基本类
 * 注意：此为抽象类，无法被实例化
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link https://www.sylibs.com/
 * @copyright Copyright (c) 2015-2019 ShuangYa
 * @license https://syframework.sylibs.com/license.html
 */
namespace Sy\DB;

use \PDOException;
use Sy\App;
use Sy\Exception\Exception;
use Sy\Exception\DBException;

abstract class PDOAbstract {
	protected $connection = null;
	protected $config = null;
	/**
	 * 抽象函数：连接
	 * @access protected
	 * @param string $id
	 */
	abstract protected function connect();
	/**
	 * 构造函数，自动连接
	 * @access public
	 */
	public function __construct() {
		if (!class_exists('PDO', FALSE)) {
			throw new SYException('Class "PDO" is required');
		}
		$this->connect();
	}
	/**
	 * 执行查询
	 * @access public
	 * @param string $sql SQL语句
	 * @param array $data 参数
	 * @return array
	 */
	public function query(string $sql, $data = null) {
		$st = $this->connection->prepare($sql);
		if ($st === FALSE) {
			$e = $this->connection->errorInfo();
			throw new DBException($e[2]);
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
			$result = $st->execute();
			if ($result === FALSE) {
				$e = $st->errorInfo();
				throw new DBException($e[2]);
			}
		} catch (\PDOException $e) {
			throw new DBException($e->getMessage());
		}
		if (stripos($sql, 'delete') === 0 || stripos($sql, 'insert') === 0 || stripos($sql, 'update') === 0) {
			$result = [
				'_affected_rows' => $st->rowCount()
			];
			if (stripos($sql, 'insert') === 0) {
				$result['_insert_id'] = $this->connection->lastInsertId();
			}
			return $result;
		}
		$st->setFetchMode(\PDO::FETCH_ASSOC);
		return $st->fetchAll();
	}
	public function getConnection() {
		return $this->connection;
	}
}