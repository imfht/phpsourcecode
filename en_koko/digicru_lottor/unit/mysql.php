<?php
class ConnectionMySQL{
	//主机
	private $host="localhost";
	//数据库的username
	private $name="root";
	//数据库的password
	private $pass="abshanghai";
	//数据库名称
	private $table="WeixinDigicru";
	//编码形式
	private $ut="utf8";


	//构造函数
	function __construct(){
		$this->connect();
	}

	//数据库的链接
	function connect(){
		$link=mysql_connect($this->host,$this->name,$this->pass) or die ($this->error());
		mysql_select_db($this->table,$link) or die("没该数据库：".$this->table);
		mysql_query("SET NAMES '$this->ut'");
	}

	function query($sql, $type = '') {
		if(!($query = mysql_query($sql))) $this->show('Say:', $sql);
		return $query;
	}

	function show($message = '', $sql = '') {
		if(!$sql) echo $message;
		else echo $message.'<br>'.$sql;
	}

	function affected_rows() {
		return mysql_affected_rows();
	}

	function result($query, $row) {
		return mysql_result($query, $row);
	}

	function num_rows($query) {
		return @mysql_num_rows($query);
	}

	function num_fields($query) {
		return mysql_num_fields($query);
	}

	function free_result($query) {
		return mysql_free_result($query);
	}

	function insert_id() {
		return mysql_insert_id();
	}

	function fetch_row($query) {
		return mysql_fetch_row($query);
	}

	function version() {
		return mysql_get_server_info();
	}

	function close() {
		return mysql_close();
	}

	function querySqlString($sql){
		return mysql_query($sql);
	}
	function fn_select($sql){

		$result= mysql_fetch_array(mysql_query($sql));

		return $result;
		//return $this->query($sql);
	}

}


?>