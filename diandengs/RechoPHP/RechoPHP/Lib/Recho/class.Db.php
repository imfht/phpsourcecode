<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

/**
 	$config['dbmaster'] = array(//数据库,(数据库地址, 用户名, 密码, 数据库名)
		array('192.168.0.1', 'root', '', ''),
	);
 */
class Db{
	var $arrServers;
	var $persist = false;
	
	var $conn = NULL;
	var $result = false;
	var $fields;
	var $check_fields;
	var $tbname;
	var $addNewFlag=false;
	var $opened = false;
	public function __construct( $arrServers){
		 $this->arrServers = $arrServers;
	}
	public function open(){
		if(! $this->opened){
			$rand = rand(0, count($this->arrServers)-1);  //随机获取一个数据库
			$arrServer = $this->arrServers[$rand];
			$this->host = $arrServer[0];
			$this->user = $arrServer[1];
			$this->password = $arrServer[2];
			$this->database = $arrServer[3];
			$this->opened = true; //标志已经连接过一次了
			if($this->connect()===false) return false;
			if($this->select_db()===false) return false;
		}
		return $this->conn;
	}
	public function affectedRows(){
		return @mysql_affected_rows($this->conn);
	}
	public function close(){
		if( $this->opened){
			$this->opened = false;
			@mysql_close($this->conn);
		}
	}
	public function connect(){
		if(is_resource($this->conn)){
			return true;
		}
		if ($this->persist){
			$this->conn = @mysql_pconnect($this->host, $this->user, $this->password) or $this->errorlog();
		}else{
			$this->conn = @mysql_connect($this->host, $this->user, $this->password) or $this->errorlog();
		}
		if(!is_resource($this->conn)){
			return false;
		}else if(mysql_get_server_info( $this->conn) > '4.1'){
			$this->query("SET SQL_MODE='',CHARACTER_SET_CONNECTION='utf8',CHARACTER_SET_RESULTS='utf8',CHARACTER_SET_CLIENT='binary',NAMES 'utf8'");
		}
		return true;
	}
	public function select_db($dbname=""){
		if($dbname==""){
			$dbname = $this->database;
		}
		return  $dbname=='' ? true : @mysql_select_db($dbname, $this->conn);
	}
	public function error(){ //Get last error
	    return @mysql_error( $this->conn);
	}
	public function errorno(){ //Get error number
	    return @mysql_errno( $this->conn);
	}
	public function query($sql = ''){ //Execute the sql query
		$this->opened ? '' : $this->open();
	    $this->result = mysql_query($sql, $this->conn) or $this->errorlog( $sql);
	    return $this->result;
	}
	public function numRows($result=null){ //Return number of rows in selected table
		if(!is_resource($result))
			$result = $this->result;
	    return @mysql_num_rows($result);
	}
	public function fieldName($field, $result=null){
		if(!is_resource($result))
			$result = $this->result;
	   return @mysql_field_name($result,$field);
	}
	public function insertID(){
	    return @mysql_insert_id($this->conn);
	}
	
	public function data_seek($arg1,$row=0){ ///Move internal result pointer
		if(is_resource($arg1))
			$result = $arg1;
		else
			$result = $this->result;
		
		if(!is_resource($arg1) && !is_null($arg1))
			$row = $arg1;
	
		return mysql_data_seek($result,$row);
	}
	
	public function fetchRow($result=null){
		if(!is_resource($result))
			$result = $this->result;
	    return @mysql_fetch_row($result);
	}
	
	public function fetchObject($result=null){
		if(!is_resource($result))
			$result = $this->result;
	    return @mysql_fetch_object($result);
	}
	public function fetchArray($arg1=null,$mode=MYSQL_BOTH){
		if(is_resource($arg1))
			$result = $arg1;
		else
			$result = $this->result;
		
		if(!is_resource($arg1) && !is_null($arg1))
			$mode = $arg1;
		$array = @mysql_fetch_array($result, $mode);
	    return is_array( $array) ? $array : array();
	}
	public function fetchAssoc($result=null){
		if(!is_resource($result))
			$result = $this->result;
	    return @mysql_fetch_assoc($result);
	}
	public function freeResult($result=null){
		if(!is_resource($result))
			$result = $this->result;
	    return @mysql_free_result($result);
	}
	public function getSingleResult($sql){
		$result = $this->query($sql);
		$row = $this->fetchArray($result,MYSQL_NUM);
		$return=$row[0];
		return $return;
	}
	
	public function addNew($table_name){
	   $this->fields=array();
	   $this->addNewFlag=true;
	   $this->tbname=$table_name;
	}
	
	public function edit($table_name){
	   $this->fields=array();
	   $this->check_fields=array();
	   $this->addNewFlag=false;
	   $this->tbname=$table_name;
	}
	
	public function update(){
	 foreach($this->fields as $field_name=>$value)
	 {
		 $qry.=$field_name."='".$value."',";
	 }
	 $qry=substr($qry,0,strlen($qry)-1);
	
	  if($this->addNewFlag)
	    $qry="INSERT INTO ".$this->tbname." SET ".$qry;
	  else
	  {
	   $qry="UPDATE ".$this->tbname." SET ".$qry;
	   if(count($this->check_fields)>0 && is_array($this->check_fields))
	   {
	       $qry.=" WHERE ";
	       foreach($this->check_fields as $field_name=>$value)
	           $qry.=$field_name."='".$value."' AND ";
	       $qry=substr($qry,0,strlen($qry)-5);
	   }
	   else if(!empty($this->check_fields))
	   {
	       $qry.=" WHERE ".$this->check_fields." ";
	   }
	  }
	 return $this->query($qry);
	}
	
	public function insert($tbl, $arrData){
		$sql = "INSERT INTO $tbl SET ";
		foreach ((array)$arrData as $key => $value){
			$sql .= "`$key`='$value',";
		}
	
		$this->query(substr($sql,0,-1));
		return $this->insertID();
	}
	
	public function getAll($sql, $mode=MYSQL_BOTH){
		$result = $this->query( $sql);
		$temp = array();
		while ($array = $this->fetchArray($result,$mode)) {
			$temp[] = $array;        		
		}
		return (array)$temp;
	}
	
	public function getOne($sql, $mode=MYSQL_BOTH){
		$result = $this->query( $sql);
		return $this->fetchArray($result,$mode);
	}
	
	public function fromOneTable($tbl, $where=1, $item=array('*'), $multi=0, $mode=MYSQL_BOTH){
		$query = "SELECT ";
		foreach ((array)$item as $key => $value){
			$query .= $value . ',';
		}
		$query = substr($query, 0, -1) . " FROM $tbl WHERE $where" . ($multi ? '' : ' Limit 1');
		return $multi ? $this->getAll($query, $mode) : $this->getOne($query, $mode);
	}
	
	/**
	 * 缓存多行数据
	 */
	public function getCacheAll($sql, $expire, $mode=MYSQL_BOTH, $key=false){
		$key = $key===false ? md5($sql) : $key;
		if( ($temp = ocache::cache()->get($key)) === false){
			$temp = $this->getAll($sql, $mode);
			ocache::cache()->set($key, $temp, $expire);
		}
		return $temp;        	
	}
	
	/**
	 * 缓存一行数据
	 */
	public function getCacheOne($sql, $expire, $mode=MYSQL_BOTH, $key=false){
		$key = $key===false ? md5($sql) : $key;
		if( ($temp = ocache::cache()->get($key)) === false){
			$temp = $this->getOne($sql, $mode);
			ocache::cache()->set($key, $temp, $expire);
		}
		return $temp;        	
	}
	
	/**
	 * 安全性检测.调用escape存入的,一定要调unescape取出
	 */
	public function escape( $string){
		return @mysql_escape_string( trim($string));
	}

	public function unescape( $string){
		return stripslashes( $string);
	}
	
	/**
	 * 事务处理章节
	 */
	public function Start(){
		$this->query("START TRANSACTION");
	}
	public function Commit(){
		$this->query("COMMIT");
	}
	public function CommitId(){
		$aId = $this->getOne('SELECT LAST_INSERT_ID()', MYSQL_NUM);
		return (int)$aId[0];
	}
	public function Rollback(){
		$this->query("ROLLBACK");
	}
	
	private function errorlog( $msg='' ){
		$error = date('H:i:s').":\n".$this->errorno() . ":\nmsg:".$this->error() . $msg . ";\n";
		$file = RECHO_PHP . 'Runtime/Log/mysql.txt';
		@file_put_contents($file, "{$error}\n", @filesize($file)<512*1024 ? FILE_APPEND : null);
		die('DB Invalid!!!');
	}
	
	public function __destruct(){
		$this->close();
	}
}