<?php
//版权所有(C) 2014 www.ilinei.com

namespace ilinei;

/**
 * MySQL数据库访问
 * @author sigmazel
 * @since v1.0
 */
class database_mysql{
	private $querynum = 0;
	private $curlink;
	private $link = array();
	private $config = array();
	private $sqls = array();
	private $error = array( 'db_error' => '<b>$message</b>$errorno<br />$info$sql<br /><br />',
							'db_error_message' => '<b>error message</b>: $dberror<br />',
							'db_error_sql' => '<b>SQL</b>: $sql<br />',
							'db_error_no' => ' [$dberrno]',
							'db_notfound_config' => 'config file "config_global.php" not fined or not exists。',
							'db_notconnect' => 'can not connect to database server.',
							'db_query_error' => 'query sql error',
							'db_config_db_not_found' => 'database config var error，please check config_global.php',
							);
	private $errormsg = '';
	private $log_file;
	private $hostid = 1;
	
	public function __construct($config = array()){
		if(!empty($config)){
			$this->set_config($config);
		}
		
		if(!is_dir(ROOTPATH.'/_cache/database')){
			@mkdir(ROOTPATH.'/_cache/database', 0755, true);
			@chown(ROOTPATH.'/_cache/database', 'apache');
		}
		
		$this->log_file = ROOTPATH.'/_cache/database/log_'.date('YmdH').'.txt';
	}

	public function __destruct(){
		foreach ($GLOBALS['DATABASELINKS'] as $link) @mysql_close($link);
	}
	
	public function get_sqls(){
		return $this->sqls;
	}
	
	public function get_config(){
		return $this->config;
	}
	
	private function set_config($config){
		$this->config = &$config['database'];
	}
	
	public function connect($hostid = 1){
		$hostid + 0 <= 0 && $hostid = 1;
		
		if(empty($this->config) || empty($this->config[$hostid])) $this->halt('config_db_not_found');
		
		if($GLOBALS['DATABASELINKS'][$hostid]) $this->link[$hostid] = $GLOBALS['DATABASELINKS'][$hostid];
		else $this->link[$hostid] = $this->dbconnect($this->config[$hostid]['dbhost'], $this->config[$hostid]['dbuser'], $this->config[$hostid]['dbpw'], 
													$this->config[$hostid]['dbcharset'], $this->config[$hostid]['dbname'], $this->config[$hostid]['pconnect']);
		
		$this->hostid = $hostid;
		$this->curlink = $GLOBALS['DATABASELINKS'][$hostid] = $this->link[$hostid];
	}
	
	private function dbconnect($dbhost, $dbuser, $dbpw, $dbcharset, $dbname, $pconnect){
		$link = null;
		$func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
		if(!$link = @$func($dbhost, $dbuser, $dbpw, 1)) $this->halt('notconnect');
		else {
			$this->curlink = $link;
			if($this->version() > '4.1') {
				$dbcharset = $dbcharset ? $dbcharset : $this->config[1]['dbcharset'];
				$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
				$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
				$serverset && mysql_query("SET $serverset", $link);
			}
			
			$dbname && @mysql_select_db($dbname, $link);
		}
		
		return $link;
	}

	private function select_db($dbname){
		return mysql_select_db($dbname, $this->curlink);
	}

	public function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}

	public function fetch_first($sql){
		return $this->fetch_array($this->query($sql));
	}

	public function fetch_row($query){
		$query = mysql_fetch_row($query);
		return $query;
	}
	
	public function result($query, $row = 0){
		$query = @mysql_result($query, $row);
		return $query;
	}
	
	public function result_first($sql){
		return $this->result($this->query($sql), 0);
	}

	public function query($sql, $type = ''){
		global $setting;
		
		$this->errormsg = '';
		
		if(defined('DEBUG') && DEBUG) $starttime = get_microtime();
		
		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ? 'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($sql, $this->curlink))) {
			if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
				$this->connect();
				return $this->query($sql, 'RETRY'.$type);
			}
			
			if($type != 'SILENT' && substr($type, 5) != 'SILENT') $this->halt('query_error', $sql);
		}
		
		if($setting['SiteLogDatabase'] || (defined('DEBUG') && DEBUG)){
			$this->sqls[] = get_microtime().':'.$sql.'<br/>';
			$this->log_write("[{$this->hostid}] {$sql}\r\n");
			
			if($this->errormsg()) $this->log_write($this->errormsg()."\r\n");
		}
		
		$this->querynum++;
		return $query;
	}

	public function insert_id(){
		return ($id = mysql_insert_id($this->curlink)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	public function insert($table, $data, $return_insert_id = false, $replace = false, $silent = false){
		$sql = $this->implode_field_value($data);
		$cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';
		$silent = $silent ? 'SILENT' : '';
		$return = $this->query("$cmd $table SET $sql", $silent);
		return $return_insert_id ? $this->insert_id() : $return;
	}

	public function update($table, $data, $condition, $unbuffered = false, $low_priority = false){
		$sql = $this->implode_field_value($data);
		$cmd = "UPDATE ".($low_priority ? 'LOW_PRIORITY' : '');
		$where = '';
		if(empty($condition)) $where = '1';
		else $where = $condition;
		return $this->query("$cmd $table SET $sql WHERE $where", $unbuffered ? 'UNBUFFERED' : '');
	}

    public function delete($table, $condition, $limit = 0, $unbuffered = true){
        if(empty($condition)) $where = '1';
        else $where = $condition;
        $sql = "DELETE FROM $table WHERE $where ".($limit ? "LIMIT $limit" : '');
        return $this->query($sql, ($unbuffered ? 'UNBUFFERED' : ''));
    }

	private function implode_field_value($array, $glue = ','){
		$sql = $comma = '';
		foreach ($array as $k => $v) {
			$sql .= $comma."`{$k}` = '{$v}'";
			$comma = $glue;
		}

		return $sql;
	}
	
	public function error(){
		return (($this->curlink) ? mysql_error($this->curlink) : mysql_error());
	}

	public function errno(){
		return intval(($this->curlink) ? mysql_errno($this->curlink) : mysql_errno());
	}
	
	public function errormsg(){
		return $this->errormsg;
	}
	
	public function version() {
		if(empty($this->version)) $this->version = mysql_get_server_info($this->curlink);
		return $this->version;
	}
	
	private function halt($message = '', $sql = ''){
		$dberrno = $this->errno();
		$dberror = $this->error();
		
		$errormsg = '<b>'.$this->error['db_'.$message].'</b>';
		$this->errormsg = $errormsg .= "[$dberrno]<br /><b>ERR:</b> $dberror<br />";
		
		if($sql) $errormsg .= '<b>SQL:</b> '.$sql;
		
		if($this->config['halt']) exit($errormsg);
	}
	
	private function log_write($message){
		if($this->log_file == '' || check_robot()) return true;
		
		$message = date("M d H:i:s ").'('.get_microtime().'): '.$message;
		
		$fp = fopen($this->log_file, 'a+');
		fwrite($fp, $message);
		fclose($fp);
		
		return true;
	}
}
?>