<?php
class SystemModel extends Db {
	
	public function getVersion() {
		$sql = "select VERSION()";
		return $this->fetch ( $sql );
	}
	
	//获取数据库大小
	public function getDbSize() {
		$config = require 'database.local.php';
		$sql = "SELECT sum(DATA_LENGTH)+sum(INDEX_LENGTH) size
FROM information_schema.TABLES where TABLE_SCHEMA='" . $config ['dbname'] . "'";
		$rs = $this->fetch ( $sql );
		return round($rs['size'] / (1024 * 1024),2) . "MB";
	}
	
	/**
	 * 
	 * @return SystemModel
	 */
	public static function instance() {
		return parent::_instance ( __CLASS__ );
	}
}