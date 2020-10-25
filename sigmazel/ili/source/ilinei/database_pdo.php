<?php
//版权所有(C) 2014 www.ilinei.com

namespace ilinei;

/**
 * PDO数据库访问
 * @author sigmazel
 * @since v1.0
 */
class database_pdo{
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
	
	public function __construct($config = array()) {
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
        $this->curlink = null;
        $GLOBALS['DATABASELINKS'][$this->hostid] = null;
        $this->link[$this->hostid] = null;
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
        $link = '';

	    try{
		    $link = new \PDO("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpw);

            $link->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

            if($pconnect) $link->setAttribute(\PDO::ATTR_PERSISTENT, true);

            $link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $link->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

            $this->curlink = $link;
        }catch (\PDOException $exception){
	        $this->halt($exception->getMessage());
        }

		return $link;
	}

	public function fetch_array($query, $result_type = \PDO::FETCH_ASSOC){
		return $query->fetch($result_type);
	}

	public function fetch_first($sql, $params = array()){
	    $query = $this->query($sql, $params);
        $result = $this->fetch_array($query);
        $query->closeCursor();

        return $result;
	}

	public function fetch_row($query){
        return $query->fetch(\PDO::FETCH_NUM);
	}
	
	public function result($query, $row = 0){
		$result = $this->fetch_row($query);
        $query->closeCursor();

        return $result[$row];
	}
	
	public function result_first($sql, $params = array()){
		return $this->result($this->query($sql, $params), 0);
	}

	public function query($sql, $params = array(), $mode = \PDO::FETCH_ASSOC){
		global $setting;

        if(defined('DEBUG') && DEBUG) $starttime = get_microtime();

        $this->errormsg = '';

        if($this->config[$this->hostid]['dbcharset']) $this->curlink->exec('SET NAMES '.$this->config[$this->hostid]['dbcharset']);

        if(count($params) > 0){
            $query = $this->curlink->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
            $query->execute($params);
        }else{
            $query = $this->curlink->query($sql);
        }

		if($setting['SiteLogDatabase'] || (defined('DEBUG') && DEBUG)){
			$this->sqls[] = get_microtime().':'.$sql.'<br/>';
			$this->log_write("[{$this->hostid}] {$sql}\r\n");
			
			if($this->errno() != '0000') $this->log_write($this->error()."\r\n");
		}
		
		$this->querynum++;

		return $query;
	}

	public function insert_id() {
		return $this->curlink->lastInsertId();
	}

	public function insert($table, $data, $return_insert_id = false){
        $columns = array();
        foreach ($data as $key => $val){
            $columns[] = "`{$key}`";
        }
        $columns = implode(', ', $columns);

        $values = array();
        foreach ($data as $key => $val){
            $values[] = ":{$key}";
        }
        $values = implode(', ', $values);

        $this->query("INSERT INTO {$table} ({$columns}) values ({$values})", $data);

		return $return_insert_id ? $this->insert_id() : 0;
	}

	public function update($table, $data, $condition){
        $columns = array();
        foreach ($data as $key => $val){
            $columns[] = "`{$key}` = :{$key}";
        }

        $columns = implode(', ', $columns);

        $wheresql = empty($condition) ? '1' : $condition;

        $this->query("UPDATE {$table} SET {$columns} WHERE {$wheresql}", $data);
	}

    public function delete($table, $condition){
        $wheresql = empty($condition) ? '1' : $condition;

        $this->query("DELETE FROM {$table} WHERE {$wheresql}");
    }

    public function trans_begin(){
        return $this->curlink->beginTransaction();
    }

    public function trans_commit(){
        return $this->curlink->commit();
    }

    public function rollback(){
        return $this->curlink->rollBack();
    }

	public function error(){
		$this->errormsg = '';

		foreach($this->curlink->errorInfo() as $key => $msg){
		    $this->errormsg .= $msg.' ';
        }

        return $this->errormsg;
	}

	public function errno(){
		return $this->curlink->errorCode();
	}
	
	public function errormsg(){
		return $this->errormsg;
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