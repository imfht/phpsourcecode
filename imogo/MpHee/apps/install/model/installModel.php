<?php
class installModel extends baseModel{
  
	public function __construct( $database= 'DB' ){
		$this->model = self::connect( config($database) );
		$this->db = $this->model->db;
	}
	
	//安装数据库
	public function installSql($sqlFile){
		
		$sql="CREATE DATABASE IF NOT EXISTS `". config('DB_NAME') ."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$this->query($sql);
		$this->db->select_db( config('DB_NAME') );
		
		if( !file_exists($sqlFile) ) return true;
		
		$sqls = Install::mysql($sqlFile, config('APP_ORIGINAL_PREFIX'), config('DB_PREFIX') );
		if( empty($sqls) ) return false;
		foreach($sqls as $sql){
			$this->query($sql);
		}
		return true;
	}
}