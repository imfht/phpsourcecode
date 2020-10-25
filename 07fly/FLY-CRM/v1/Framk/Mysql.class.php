<?php
 /**
 +------------------------------------------------------------------------------
 * Framk PHP框架
 +------------------------------------------------------------------------------
 * @package  Framk
 * @author   shawn fon <shawn.fon@gmail.com>
 +------------------------------------------------------------------------------
 */
 
class Mysql{
		
	private $conn;
	
 /*
  连接数据库
  */	
	public function __construct(){
		$connect = ( $GLOBALS['DB']['Persistent'] ) ? 'mysql_pconnect' : 'mysql_connect';	
		$this->conn=$connect( $GLOBALS['DB']['DBhost'].':'.$GLOBALS['DB']['DBport'], $GLOBALS['DB']['DBuser'], $GLOBALS['DB']['DBpsw'])or
		_error('dbConnectFail','请检查数据库配置',true);	
		
		mysql_select_db( $GLOBALS['DB']['DBname'],$this->conn) or _error('dbConnectFail','数据库不存在',true);	
		mysql_query('SET NAMES '. $GLOBALS['DB']['DBcharSet']);		
		
		// $host = $GLOBALS['DB']['DBhost'];
		// $name = $GLOBALS['DB']['DBname'];
		// $port = $GLOBALS['DB']['DBport'];
		// $user = $GLOBALS['DB']['DBuser'];
		// $pwd  = $GLOBALS['DB']['DBpsw'];
		
		// $this->conn=new PDO("mysql:host={$host};dbname={$name}","{$user}","{$pwd}");
		
	} 
	
 /* 
 执行SQL语句
 */		
	public function query($sql){		
		if( $result = mysql_query($sql, $this->conn) ){
			return $result;
		}else{
			_error('queryError','数据表不存在 或SQL语法错误:'.$sql, true);	
		}				 	 
	} 
	
 /*
 数组
 */		
	public function fetch_array($result, $type=MYSQL_ASSOC) {
		return mysql_fetch_array($result,$type);
	}
/*
数据记录数
*/
	public function num_rows($result){
	 return mysql_num_rows($result);		
	}
 /*
 影响记录条数
 */	
	public function affected_rows() {
		return mysql_affected_rows($this->conn);
	}		
 /* 
 获取上一次插入的id
 */	
	public function insert_id() {
		return mysql_insert_id($this->conn);
	}
/*
释放结果集
*/	
	public function free_result($result) {
		return mysql_free_result($result);
	}

/*
错误信息
*/	
	public function error() {
		return mysql_error($this->conn);
	}
	
/*
事务开始
*/	

	public function begintrans() {
		return mysql_query("BEGIN");//开始事务定义
	}

/*
事务回滚
*/	
	public function rollback() {
		return mysql_query("ROOLBACK");//开始事务定义
	}
/*
事务提交
*/	
	public function commit() {
		return mysql_query("COMMIT");//开始事务定义
	}

	public function version(){
		return mysql_get_server_info();
	}		
	
/*
析构函数关闭数据库连接
*/
	public function __destruct(){
		if( $GLOBALS['DB']['Persistent']==false) mysql_close($this->conn);
	}
 /*  +------------------------------------------------------------------------------ */
}//
?>