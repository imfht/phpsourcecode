<?php
if(!defined('DIR')){
	exit('Please correct access URL.');
}

class Mysql {
	public $conn;
	public $prefix;
    function __construct($host, $port, $name, $pass, $database, $prefix, $charset){
		$this->conn = mysqli_connect($host, $name, $pass, $database, $port) or die(mysqli_connect_error());
        $this->prefix = $prefix;
		mysqli_query($this->conn, 'SET NAMES '.$charset);
	}
	
    function query($sql, $user_result = false){
        if($user_result){
            return mysqli_query($this->conn, $sql, MYSQLI_USE_RESULT);
        }else {
		    return mysqli_query($this->conn, $sql, MYSQLI_STORE_RESULT);
        }
	}
	
    function fetchAll($sql){
		$res = $this->query($sql);
		$rows = array();
		while ($row = mysqli_fetch_assoc($res)){
			$rows[] = $row;
		}
		return $rows;
	}
	
    function fetchFirst($sql){
		$res = $this->query($sql);
		return mysqli_fetch_assoc($res);
	}
	
	function insert_id(){
		return mysqli_insert_id($this->conn);
	}
	
	function insert($tablename, $arr){
		$set = '';
		foreach((array)$arr as $k => $v){
			$set .= '`'.$k.'` = \''.addslashes($v).'\', ';
		}
		$set = substr($set, 0, -2);
        $sql = 'INSERT INTO `'.$this->prefix.$tablename.'` SET '.$set;
		return $this->query($sql);
	}
	
	function replace_into($tablename, $arr){
		$set = '';
		foreach((array)$arr as $k => $v){
			$set .= '`'.$k.'` = \''.addslashes($v).'\', ';
		}
		$set = substr($set, 0, -2);
		$sql = 'REPLACE INTO `'.$this->prefix.$tablename.'` SET '.$set;
		return $this->query($sql);
    }

    function get_server_info(){
        return mysqli_get_server_info($this->conn);
    }
	
	function close(){
		return mysqli_close($this->conn);
	}
}
