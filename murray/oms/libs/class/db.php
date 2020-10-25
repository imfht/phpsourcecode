<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package MYSQL连接类
*/

defined('INPOP') or exit('Access Denied');

class DB{

	public $connid;
	public $querynum = 0;
	public static $_instance;
	
	protected function __construct(){
		$this->connect();
	}
	
	//实例化(单例模式)
    public static function getInstance(){
        if(null === self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }
	
	public function connect($dbhost = "", $dbuser = "", $dbpw = "", $dbname = "", $pconnect = 0){
		global $_config;
		$dbhost = $dbhost ? $dbhost : $_config['db']['host'];
		$dbname = $dbname ? $dbname : $_config['db']['dbname'];
		$dbuser = $dbuser ? $dbuser : $_config['db']['user'];
		$dbpw = $dbpw ? $dbpw : $_config['db']['password'];
		$func = $pconnect == 1 ? 'mysql_pconnect' : 'mysql_connect';
		if(!$this->connid = $func($dbhost, $dbuser, $dbpw)){
			$this->halt('Can not connect to MySQL server');
		}
		
		if($this->version() > '4.1' && $_config['db']['dbcharset']){
			mysql_query("SET NAMES '".$_config['db']['dbcharset']."'" , $this->connid);
		}
		
		if($this->version() > '5.0'){
			mysql_query("SET sql_mode=''" , $this->connid);
		}
		if($dbname){
			if(!@mysql_select_db($dbname , $this->connid)){
				$this->halt('Cannot use database '.$dbname);
			}
		}
		return $this->connid;
	}

	public function select_db($dbname){
		return mysql_select_db($dbname , $this->connid);
	}

	public function query($sql , $type = '' , $expires = 3600, $dbname = ''){
		$func = $type == 'UNBUFFERED' ? 'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($sql , $this->connid)) && $type != 'SILENT'){
			$this->halt('MySQL Query Error', $sql);
		}
		$this->querynum++;
		return $query;
	}

	public function get_one($sql, $type = '', $expires = 3600, $dbname = ''){
		$query = $this->query($sql, $type, $expires, $dbname);
		$rs = $this->fetch_array($query);
		$this->free_result($query);
		return $rs ;
	}

	public function fetch_array($query, $result_type = MYSQL_ASSOC){
		return @mysql_fetch_array($query, $result_type);
	}

	public function affected_rows(){
		return @mysql_affected_rows($this->connid);
	}

	public function num_rows($query){
		return @mysql_num_rows($query);
	}

	public function num_fields($query){
		return @mysql_num_fields($query);
	}

	public function result($query, $row){
		return @mysql_result($query, $row);
	}

	public function free_result($query){
		return @mysql_free_result($query);
	}

	public function insert_id(){
		return mysql_insert_id($this->connid);
	}

	public function fetch_row($query){
		return mysql_fetch_row($query);
	}

	public function version(){
		return mysql_get_server_info($this->connid);
	}

	public function close(){
		return mysql_close($this->connid);
	}

	public function error(){
		return @mysql_error($this->connid);
	}

	public function errno(){
		return intval(@mysql_errno($this->connid));
	}

	public function halt($message = '', $sql = ''){
		exit("MySQL Query:$sql <br> MySQL Error:".$this->error()." <br> MySQL Errno:".$this->errno()." <br> Message:$message");
	}
}
?>