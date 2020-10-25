<?php

/**
 * PDO_SQLite支持类
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
use \PDO;
use \sy\base\Pdo as YPdo;
use \sy\lib\YHtml;
use \sy\base\SYException;
use \sy\base\SYDException;

class Sqlite extends YPdo {
	protected $dbtype = 'SQLite';
	/**
	 * 自动连接
	 * @access protected
	 */
	protected function autoConnect() {
		if (Sy::$app->has('sqlite')) {
			$this->setParam(Sy::$app->get('sqlite'));
		}
	}
	/**
	 * 连接到MySQL
	 * @access protected
	 */
	protected function connect() {
		$id = $this->current;
		//对老版本的支持
		if ($this->dbInfo[$id]['version'] === 'sqlite3') {
			$dsn = 'sqlite:';
		} else {
			$dsn = 'sqlite2:';
		}
		$path = str_replace('@app/', Sy::$appDir, $this->dbInfo[$id]['path']);
		$dsn .= $path;
		try {
			$this->link[$id] = new PDO($dsn);
			$this->result[$id] = [];
		} catch (PDOException $e) {
			throw new SYDException(YHtml::encode($e->getMessage), $this->dbtype, $dsn);
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
		if (!preg_match('/limit ([0-9,]+)$/i', $sql)) {
			$sql .= ' LIMIT 0,1';
		}
		return $this->query($sql, $data);
	}
}
