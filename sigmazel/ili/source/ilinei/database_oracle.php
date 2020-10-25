<?php
//版权所有(C) 2014 www.ilinei.com

namespace ilinei;

/**
 * Oracle数据库访问
 * @author sigmazel
 * @since v1.0
 */
class database_oracle{
	private $version = '';
	private $query_num = 0;
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
	
	public function __construct($config = array()) {
		if(!empty($config)) {
			$this->set_config($config);
		}
		
		if(!is_dir(ROOTPATH.'/_cache/database')){
			@mkdir(ROOTPATH.'/_cache/database', 0755, true);
			@chown(ROOTPATH.'/_cache/database', 'apache');
		}
		
		$this->log_file = ROOTPATH.'/_cache/database/log_'.date('YmdH').'.txt';
	}
	
	public function __destruct(){
		foreach ($GLOBALS['DATABASELINKS'] as $link) @oci_close($link);
	}
	
	public function get_sqls(){
		return $this->sqls;
	}
	
	public function get_config(){
		return $this->config;
	}
	
	private function set_config($config) {
		$this->config = &$config['database'];
	}
	
	public function connect($hostid = 1) {
		$hostid + 0 <= 0 && $hostid = 1;
		
		if(empty($this->config) || empty($this->config[$hostid])) $this->halt('config_db_not_found');
		
		if($GLOBALS['DATABASELINKS'][$hostid]) $this->link[$hostid] = $GLOBALS['DATABASELINKS'][$hostid];
		else $this->link[$hostid] = $this->dbconnect($this->config[$hostid]['dbhost'], $this->config[$hostid]['dbuser'], $this->config[$hostid]['dbpw'], 
													$this->config[$hostid]['dbcharset'], $this->config[$hostid]['dbname'], $this->config[$hostid]['pconnect']);
		
		$this->hostid = $hostid;
		$this->curlink = $GLOBALS['DATABASELINKS'][$hostid] = $this->link[$hostid];
	}
	
	private function dbconnect($dbhost, $dbuser, $dbpw, $dbcharset, $dbname, $pconnect) {
		$link = null;
		$func = empty($pconnect) ? 'oci_connect' : 'oci_pconnect';
		if(!$link = @$func($dbuser, $dbpw, "{$dbhost}/{$dbname}", $dbcharset)) $this->halt('notconnect');
		else $this->curlink = $link;
		
		return $link;
	}

	public function fetch_array($query, $result_type = OCI_ASSOC){
		return oci_fetch_array($query, $result_type);
	}

	public function fetch_first($sql){
		return $this->fetch_array($this->query($sql));
	}

	public function fetch_row($query){
		$query = oci_fetch_row($query);
		return $query;
	}
	
	public function result($query, $row = 0){
		$query = @oci_fetch_row($query);
		return $query[$row];
	}

	public function result_first($sql){
		return $this->result($this->query($sql));
	}

	public function query($sql, $bind = false){
		global $setting;
		
		$this->errormsg = '';
		
		if(defined('DEBUG') && DEBUG) $starttime = get_microtime();
		
		$stmt = oci_parse($this->curlink, $sql);
		if(!$bind) oci_execute($stmt);
		
		if($setting['SiteLogDatabase'] || (defined('DEBUG') && DEBUG)){
			$this->sqls[] = get_microtime().':'.$sql.'<br/>';
			$this->log_write("[{$this->hostid}] {$sql}\r\n");
			
			$error = $this->error($stmt);
			if($error && $error['message']) $this->log_write($error['message']."\r\n");
		}
		
		$this->query_num++;
		
		return $stmt;
	}

	public function insert($table, $data){
		$value = $column = '';
		$clobs = array();
		
		foreach($data as $k => $v) {
			if($k == 'SIZE' || $k == 'LOCK' || $k == 'LEVEL' || $k == 'GROUP' || $k == 'COMMENT' || $k == 'CONNECT') $column .= "\"{$k}\",";
			else $column .= "{$k},";
			
			if(is_array($v) && $v['TYPE'] == 'CLOB'){
				 $value .= "EMPTY_CLOB(),";
				 $clobs[] = $k;
			}elseif(is_timestamp($v)) $value .= "TO_DATE('{$v}', 'YYYY-MM-DD HH24:MI:SS'),";
			elseif(is_datetime($v)) $value .= "TO_DATE('{$v}', 'YYYY-MM-DD HH24:MI'),";
			elseif(substr($v, -8) == '.NEXTVAL') $value .= "{$v},";
			else $value .= "'{$v}',";
		}
		
		$column && $column = substr($column, 0, -1);
		$value && $value = substr($value, 0, -1);
		
		return $this->query("INSERT INTO {$table}($column) VALUES($value)");
	}

	public function update($table, $data, $condition){
		$sql = '';
		$clobs = array();
		
		foreach($data as $k => $v) {
			if($k == 'SIZE' || $k == 'LOCK' || $k == 'LEVEL' || $k == 'GROUP' || $k == 'COMMENT' || $k == 'CONNECT') $k = "\"{$k}\"";
			
			if(is_array($v) && $v['TYPE'] == 'CLOB'){
				$sql .= "{$k} = EMPTY_CLOB(),";
				$clobs[$k] = $k;
			}elseif(is_timestamp($v)) $sql .= "{$k} = TO_DATE('{$v}', 'YYYY-MM-DD HH24:MI:SS'),";
			elseif(is_datetime($v)) $sql .= "{$k} = TO_DATE('{$v}', 'YYYY-MM-DD HH24:MI'),";
			elseif(substr($v, -8) == '.NEXTVAL') $sql .= "{$k} = {$v},";
			else $sql .= "{$k} = '{$v}',";
		}
		
		$sql && $sql = substr($sql, 0, -1);
		
		$where = '';
		if(empty($condition)) $where = '1=1';
		else $where = $condition;
		
		if(count($clobs) > 0){
			$column = $value = '';
			foreach($clobs as $key => $val){
				$column .= "{$key},";
				$value .= ":{$val},";
			}
			
			$column && $column = substr($column, 0, -1);
			$value && $value = substr($value, 0, -1);
			
			$stmt = $this->query("UPDATE {$table} SET {$sql} WHERE {$where} RETURNING {$column} INTO {$value}", true);
			
			foreach($clobs as $key => $val){
				$clob = oci_new_descriptor($this->curlink, OCI_D_LOB);
				oci_bind_by_name($stmt, ":{$val}", $clob, -1, OCI_B_CLOB);
				$clobs[$key] = $clob;
			}
			
			oci_execute($stmt, OCI_DEFAULT);
			foreach($clobs as $key => $clob) $clob->save($data[$key]['VALUE']);
			
			return oci_commit($this->curlink);
		}else return $this->query("UPDATE {$table} SET {$sql} WHERE {$where}");
	}

    public function delete($table, $condition) {
        if(empty($condition)) $where = '1=1';
        else $where = $condition;

        return $this->query("DELETE FROM {$table} WHERE {$where}");
    }
	
	public function error($stmt){
		return $stmt ? oci_error($stmt) : oci_error();
	}

	private function halt($message = '', $sql = '') {
        $dberror = $this->error();
		
		$errormsg = '<b>'.$this->error['db_'.$message].'</b>';
		$errormsg .= "<b>ERR:</b> $dberror<br />";
		
		if($sql) $errormsg .= '<b>SQL:</b> '.$sql;
		
		if($this->config['halt']) exit($errormsg);
	}
	
	private function log_write($message){
		if ($this->log_file == '' || check_robot()) return true;
		
		$message = date("M d H:i:s ").'('.get_microtime().'): '.$message;
		
		$fp = fopen($this->log_file, 'a+');
		fwrite($fp, $message);
		fclose($fp);
		
		return true;
	}
}
?>