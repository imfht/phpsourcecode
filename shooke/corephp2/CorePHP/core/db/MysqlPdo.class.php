<?php
namespace Core\Db;
use \PDO,\PDOException,\Exception,\Core\Config;
/**
 * @author shooke
 * 数据库驱动类
 * 底层的数据库链接 和sql执行 
 */
class MysqlPdo implements DriveInterface {	
    public $link = NULL; //数据库链接	
    public $sql = "";
	private $affectedRows = 0; //受影响条数
	private $dbConfig = array();
	private $error = array();
	private $PDOStatement=null;//pdo返回数据集	

	public function __construct($config = array()){
		$this->dbConfig = $config;
	    try {            
            $dns = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']}";
            $this->link = new PDO($dns, $config['DB_USER'], $config['DB_PWD']);
            $this->link->setAttribute(PDO::ATTR_PERSISTENT, $config['DB_PCONNECT']);  // 设置数据库连接为持久连接
            //$this->link->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);//关闭自动提交事务
			$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // 设置抛出错误
			$this->link->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);  // 指定数据库返回的NULL值在php中对应的数值 不变
			$this->link->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL); // 强制PDO 获取的表字段字符的大小写转换,原样使用列值
			$this->link->exec("SET NAMES {$config['DB_CHARSET']}");//设置编码  				
        } catch (PDOException $e) {  
            $this->error[1] = $e->getCode();//errorno错误码
            $this->error[2] = $e->getMessage();//error错误信息
            $this->error['content'] = $e->getMessage(); 
        }
	}
    //事务开始
	public function begin(){
	    $this->link->beginTransaction();
	}
	//事务提交
	public function commit(){
	    $this->link->commit();
	}
	//事务回滚
	public function rollBack(){
	    $this->link->rollBack();
	}
	//执行sql查询
	public function query($sql, $params = array()) {
		foreach($params as $k => $v){
			$sql = str_replace(':'.$k, $this->escape($v), $sql);
		}
		$this->sql = $sql;
		try {
		    $this->free();//释放先前的资源
		    $this->PDOStatement=$this->link->prepare($this->sql);
		    $res = $this->PDOStatement->execute();
			return $res;
		}catch (PDOException $e){
			$this->error = empty($this->PDOStatement) ? $this->link->errorInfo() : $this->PDOStatement->errorInfo();
            $this->error['content'] = $e->getMessage();
			$this->error();
		}
	}

	//执行sql命令
	public function execute($sql, $params = array()) {
		foreach($params as $k => $v){
			$sql = str_replace(':'.$k, $this->escape($v), $sql);
		}
		$this->sql = $sql;
		try {
			$this->affectedRows = $this->link->exec($sql);//用于affectedRows()获取返回结果
			return $this->affectedRows;
		}catch (PDOException $e){
			$this->error = $this->link->errorInfo();
            $this->error['content'] = $e->getMessage();
			$this->error();
		}
	}

	//从结果集中取得一行作为关联数组，或数字数组，或二者兼有
	public function fetch($result_type = PDO::FETCH_ASSOC) {
		return  $this->PDOStatement->fetch($result_type);
	}
	//从结果集中取得所有行作为关联数组，或数字数组，或二者兼有
	public function fetchAll($result_type = PDO::FETCH_ASSOC) {
	    return  $this->PDOStatement->fetchAll($result_type);
	}
	//取得前一次 MySQL 操作所影响的记录行数
	public function affectedRows() {
		return $this->affectedRows;
	}
	//获取上一次插入的id
	public function lastId() {
		return $this->link->lastInsertId();
	}
	//获取SQL语句
	public function getSql(){
	    return $this->sql;
	}
	//获取数据库表
	public function getTables($database){
		$this->sql = "SHOW TABLES FROM `{$database}`";
		$this->query($this->sql);
		$data = array();
		while($row = $this->fetch()){
			$data[] = $row['Tables_in_'.$database];
		}
		return $data;
	}
	//获取表结构
	public function getFields($table) {
		$this->sql = "SHOW FULL FIELDS FROM {$table}";
		$this->query($this->sql);
		$data = array();
		while($row = $this->fetch()){
			$data[] = $row;
		}
		return $data;
	}
	public function formatFields($table){
	    $this->sql = "SHOW FULL FIELDS FROM {$table}";
	    $this->query($this->sql);
	    $data = array();
	    while($row = $this->fetch()){
	        $temp = array(
// 	            'name' => $row['Field'],//字段名称
	            'type' => $row['Type'],	//字段类型
	            'charset'=>$row['Collation'],//字符集
	            'isnull' => strtolower($row['Null'])=='yes' ? true : false,//是否为空 false为非空
	            'primary' => strtolower($row['Key'])=='pri' ? true : false,//是否是主键
	            'default' => $row['Defaulte'],	//默认值
	            'autoinc' => strtolower($row['Extra'])=='auto_increment' ? true : false,//自动累加
	            'privileges'=>$row['Privileges'], //特权select,insert,update,references
	            'comment'=>$row['Comment'],//备注
	        );
	        $data[$row['Field']] = $temp;
	    }
	    return $data;
	    
	}
	//获取行数
	public function count($table,$where,$field='*') {
		$this->sql = "SELECT count($field) FROM $table $where";
		$this->query($this->sql);
		return $this->PDOStatement->fetchColumn();
	}
	//取得数据库版本
    public function version(){
        return $this->link->getAttribute(constant("PDO::ATTR_SERVER_VERSION"));
    }
	//数据过滤
	public function escape($value) {
		if( is_array($value) ) {
			return array_map(array($this, 'escape'), $value);
		} else {
			return "'" . addslashes($value) . "'";
		}
	}

	//解析待添加或修改的数据
	public function parseData($options, $type) {
		//如果数据是字符串，直接返回
		if(is_string($options['data'])) {
			return $options['data'];
		}
		//为空返回false
		if(empty($options['data'])){
		    return false;
		}		
		if( is_array($options) && !empty($options) ) {
			switch($type){
				case 'add':
				    $fields = array();//插入字段
				    $Values = array();//插入数据
				    $tempData = array();//临时存放防注入处理后的数据
					//如果第一个元素是数组则进行二维数组多条插入处理
					if(is_array($options['data'][0])){
					    $fields = array_keys($options['data'][0]);//字段处理
					    $tempData = $this->escape( array_values($options['data']) );//防注入处理
					    //拆分为插入语句
					    foreach ($tempData as $val){
					        $Values[]    = "(".implode(",", $val).")";					        
					    }
					    //最终插入语句
					    return " (`" . implode("`,`", $fields) . "`) VALUES " . implode(",", $Values) ;
					}else{
					    //一位数组单条插入处理
					    $fields = array_keys($options['data']);
					    $Values = $this->escape( array_values($options['data']) );
					    return " (`" . implode("`,`", $fields) . "`) VALUES (" . implode(",", $Values) . ") ";
					}
					
				case 'save':
				    $data = array();
					foreach($options['data'] as $key => $value) {
						$data[] = " `$key` = " . $this->escape($value);
					}
					return implode(',', $data);
				default:return false;
			}
		}
		return false;
	}

	//解析查询条件
	public function parseCondition($options) {
		$condition = "";
		if(!empty($options['where'])) {
			$condition = " WHERE ";
			if(is_string($options['where'])) {
				$condition .= $options['where'];
			} else if(is_array($options['where'])) {
				foreach($options['where'] as $key => $value) {
					//如果有$where[1]="id='f'"则不进行方过滤处理需要在取得变量时进行方过滤处理
					$condition .= is_numeric($key) ? $value . " AND " : " `$key` = " . $this->escape($value) . " AND ";
				}
				$condition = substr($condition, 0,-4);
			} else {
				$condition = "";
			}
		}

		if( !empty($options['group']) && is_string($options['group']) ) {
			$condition .= " GROUP BY " . $options['group'];
		}
		if( !empty($options['having']) && is_string($options['having']) ) {
			$condition .= " HAVING " .  $options['having'];
		}
		if( !empty($options['order']) && is_string($options['order']) ) {
			$condition .= " ORDER BY " .  $options['order'];
		}
		if( !empty($options['limit']) && (is_string($options['limit']) || is_numeric($options['limit'])) ) {
			$condition .= " LIMIT " .  $options['limit'];
		}
		if( empty($condition) ) return "";
		return $condition;
	}
	//返回链接状态
    public function success(){
        return empty($this->error);
    }
	//输出错误信息
	public function error(){    
		if( DEBUG ){
			$error_sql = str_replace(Config::get('DB_PREFIX'),'[PRE]',$this->sql);
			$error = str_replace(Config::get('DB_PREFIX'),'[PRE]',$this->error[2]);
			$errorno = $this->error[1];
			$message = str_replace(Config::get('DB_PREFIX'),'[PRE]',$this->error['content']);
			$str = " {$message}<br>
					<b>SQL</b>: {$error_sql}<br>
					<b>错误详情</b>: {$error}<br>
					<b>错误代码</b>:{$errorno}<br>"; 
		} else {
			$str = "<b>出错</b>: $message<br>";
		}
		throw new Exception($str);
	}
	//释放资源
	public function free(){
	    $this->PDOStatement = null;
	}
    //断开链接
	public function close(){
	    $this->link = null;
	}    
	//关闭数据库
	public function __destruct() {
		$this->close();
	}
}