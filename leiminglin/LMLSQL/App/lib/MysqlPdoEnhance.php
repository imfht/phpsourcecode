<?php

/**
 * LMLPHP Framework
 * Copyright (c) 2014 http://lmlphp.com All rights reserved.
 * Licensed ( http://mit-license.org/ )
 * Author: leiminglin <leiminglin@126.com>
 *
 * A fully object-oriented PHP framework.
 * Keep it light, magnificent, lovely.
 *
 */

interface MysqlPdoInterface{

	public static function getInstance($config);

	public function query($sql, $params = array());

	public function getOne($sql, $params = array());

	public function getLastId();

	public function insert($table, $arrData);

	public function update($table, $arrData, $where = '', $wparams=array());

	public function delete($table, $where='', $params=array());

	public function select($table, $fields='*', $where_tail='', $params=array());
}

class MysqlPdoEnhance implements MysqlPdoInterface
{
	private static $config;
	private static $instances;
	private $db;
	public $sqls = array();
	public $debug = false;

	private function __construct() {
		$dsn = 'mysql:host='.self::$config['hostname'].';port='.self::$config['hostport'].';dbname='.self::$config['database'];
		$username = self::$config['username'];
		$password = self::$config['password'];
		$options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		);
		$this->db = new PDO($dsn, $username, $password, $options);
		if ($this->db->getAttribute(PDO::ATTR_DRIVER_NAME) != 'mysql') {
			die("MySQL support not be enabled");
		}

		// fix sometimes no database selected
		$this->db->exec('USE '.self::$config['database']);
	}

	public static function getInstance($config){
		self::$config = $config;
		$flag = $config['hostname'] . $config['database'];
		if (isset(self::$instances[$flag]) && self::$instances[$flag] instanceof self){
			return self::$instances[$flag];
		}
		return self::$instances[$flag] = new self();
	}

	public function query($sql, $params = array()){
		$stmt = $this->db->prepare(trim($sql), array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));

		if($params){
			foreach ($params as $k => $v){
				if(is_int($k)){
					$k++;
				}
				if(is_array($v)){
					$value = $v['value'];
					$type = isset($v['type']) ? $v['type'] : false;
					$length = isset($v['length']) ? $v['length'] : false;
					if($type && $length){
						$stmt->bindValue($k, $value, $type, $length);
					}elseif($type){
						$stmt->bindValue($k, $value, $type);
					}else{
						$stmt->bindValue($k, $value);
					}
				}else{
					$stmt->bindValue($k, $v);
				}
			}
		}

		$stmt->execute();
		if($stmt->errorCode() != '00000'){
			throw new LmlException(implode("\n", $stmt->errorInfo()));
		}
		if ($this->debug) {
			$this->sqls[] = array($sql, $params);
		}
		if(preg_match('/^update|^insert|^replace|^delete/i', $sql)){
			return $stmt->rowCount();
		}else{
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	public function getOne($sql, $params = array()){
		$rs = $this->query($sql, $params);
		return isset($rs[0]) ? $rs[0] : array();
	}

	public function getLastId(){
		return $this->db->lastInsertId();
	}

	public function insert($table, $data){
		$keys = '';
		$place_values = '';
		$params = array();
		foreach ($data as $k => $v){
			$keys .= '`'.$k.'`,';
			$place_values .= ":".$k.",";
			$params[':'.$k] = $v;
		}
		$keys = rtrim($keys, ',');
		$place_values = rtrim($place_values, ',');
		$sql = "INSERT INTO $table ($keys) VALUES ($place_values)";
		return $this->query($sql, $params);
	}

	public function update($table, $data, $where = '', $wparams=array()){
		$sql = 'UPDATE '.$table.' SET ';
		$params = array();
		foreach ($data as $k=>$v){
			$sql .= '`'.$k.'`=:'.$k.',';
			$params[':'.$k] = $v;
		}
		$sql = rtrim($sql, ',');
		if($where){
			$sql .= ' WHERE '.$where;
		}
		return $this->query($sql, array_merge($params, $wparams));
	}

	public function delete($table, $where='', $params=array()){
		$sql = 'DELETE FROM '.$table;
		if($where){
			$sql .= ' WHERE '.$where;
		}
		return $this->query($sql, $params);
	}

	public function select($table, $fields='*', $where_tail='', $params=array()){
		$sql = 'SELECT '.$fields.' FROM '.$table;
		if($where_tail){
			$sql .= ' WHERE '.$where_tail;
		}
		return $this->query($sql, $params);
	}
	
	public function getLastSql(){
		return end($this->sqls);
	}
	
	public function getSqls(){
		return $this->sqls;
	}
}
