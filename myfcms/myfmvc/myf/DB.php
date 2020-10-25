<?php

/*
 *  @author myf
 *  @date 2014-11-13 
 *  @Description 数据库操作类库
 *  @web http://www.minyifei.cn
 */
use Myf\Mvc\Log;

class DB {
    
    protected $link = null;
    public $linkId = null;
    public $table = "";
    
    public $sql = "";
    
    //查询参数
    protected $options = array();
    
    static private $_db = null;
    static private $_dbname = null;
    public $database=null;
    
    public static function getInstance($dbname){
        if(self::$_db==null || self::$_dbname!=$dbname){
            $db = array();
            $db["pconnect"]=false;
            $db["host"]=C($dbname.".DB_HOST");
            $db["port"]=C($dbname.".DB_PORT");
            $db["user"]=C($dbname.".DB_USER");
            $db["pwd"]=C($dbname.".DB_PWD");
            $db["database"]=C($dbname.".DB_NAME");
            $db["charset"]="utf8";
            self::$_dbname = $dbname;
            self::$_db = new DB($db);
        }
        return self::$_db;
    }
    //构造函数
    function __construct($db) {
        $host = $db["host"];
        $port = $db["port"];
        $user= $db["user"];
        $password = $db["pwd"];
        $database = $db["database"];
        $charset = $db["charset"];
        $this->database=$database;
        $dsn = sprintf("mysql:host=%s;dbname=%s;port=%d;charset=%s",$host,$database,$port,$charset);
        $this->link = new \PDO($dsn, $user, $password);
        $this->linkId = getMillisecond();
    }
    
    public function getLinkID(){
        return $this->linkId;
    }
    
    /**
     * 查询一条结果
     * @return Object
     */
    public function findFirst(){
        $this->options["limit"]=1;
        return $this->find(false);
    }
    
    /**
     * 所有记录中查询
     * @param boolean $all true-返回所有记录，false-返回一条记录
     * @return Object
     */
    public function find($all=true){
        if(!isset($this->options["field"])){
            $this->options["field"]="*";
        }
        $sql = "SELECT {$this->options["field"]} FROM `{$this->table}`";
        if(isset($this->options["where"])){
            $sql.= " WHERE ". $this->options["where"];
        }
        if(isset($this->options["group"])){
            $sql.=" GROUP BY  ".$this->options["group"];
        }
        if(isset($this->options["order"])){
            $sql.=" ORDER BY ".$this->options["order"];
        }
        if(isset($this->options["limit"])){
            $sql.=" LIMIT ".$this->options["limit"];
        }
        if(!isset($this->options["bindArray"])){
            $this->options["bindArray"]=array();
        }
        $this->sql = $sql;
        $action = "select";
        if($all){
            $action= "selectAll";
        }
        return $this->execute($sql, $this->options["bindArray"], $action);
    }
    
    
    /**
     * sql查询，返回一条结果对象
     * @param String $sql sql语句
     * @param Array $bindArray 绑定参数
     * @return Object
     */
    public function findSql($sql,$bindArray=array()){
        $res = $this->execute($sql,$bindArray,"selectAll");
        return $res;
    }
    
    /**
     * sql查询，返回结果集
     * @param String $sql sql语句
     * @param Array $bindArray 绑定参数
     * @return Array
     */
    public function findFirstSql($sql,$bindArray = array()){
        $res = $this->execute($sql, $bindArray, "select");
        return $res;
    }
    
    /**
     * sql查询个数
     * @param String $sql sql语句
     * @param Array $bindArray 绑定参数
     * @return int 个数
     */
    public function countSql($sql,$bindArray=array()){
        $row = $this->findFirstSql($sql, $bindArray);
        return intval(current($row));
    }
    
    /**
     * 查询记录个数
     * @return int 数量
     */
    public function count(){
        $sql = "SELECT count(*) AS ROWCOUNT FROM `{$this->table}` ";
        $bindArray = array();
        if(isset($this->options["where"])){
            $sql.=" WHERE ".$this->options["where"];
            if(is_array($this->options["bindArray"])){
                $bindArray = $this->options["bindArray"];
            }
        }
        $res = $this->execute($sql, $bindArray, "select");
        return intval($res["ROWCOUNT"]);
    }
    
    /**
     * 添加数据
     * @param Array $data 需要添加的数据
     * @return int
     */
    public function add($data){
        if(is_array($data)){
            $sql = "INSERT INTO `{$this->table}` ";
            $fields = $values = $bindArray = array();
            foreach ($data as $key => $val) {
                $fields[]="`{$key}`";
                $values[] = ":".$key;
                $bindArray[":".$key] =$val;
            }
            $field = join(',', $fields);
            $value = join(',', $values);
            unset($fields,$values);
            $sql.="({$field}) VALUES({$value})";
            return $this->execute($sql, $bindArray, "insert");
        }else{
            return 0;
        }
    }
   
    /**
     * 更新数据
     * @param Array $data 需要更新的数据
     * @param String $where 查询条件
     * @param Array $bindArray 数据对应关系
     * @return int 影响行数
     */
    public function update($data,$where=null,$bindArray=array()){
        if(is_array($data)){
            $table = $this->table;
            $fields = $values = array();
            if(!is_array($bindArray)){
                $bindArray = array();
            }
            $sql = "UPDATE `{$table}` SET ";
            foreach ($data as $key => $val) {
                $fields[]="`{$key}`";
                $values[] = "`{$table}`.`{$key}` = :".$key;
                $bindArray[":".$key] =$val;
            }
            $value = join(",", $values);
            $sql .=$value;
            if(isset($where)){
                $sql.=" WHERE {$where} ";
            }
            return $this->execute($sql, $bindArray, "update");
        }else{
            return 0;
        }
    }
    
    /**
     * 删除记录
     * @return int 影响行数
     */ 
    public function delete(){
        $table = $this->table;
        $sql = "DELETE FROM `{$table}`";
        $bindArray = array();
        if(isset($this->options["where"])){
            $sql.=" WHERE {$this->options["where"]}";
            if(isset($this->options["bindArray"]) && is_array($this->options["bindArray"])){
                $bindArray = $this->options["bindArray"];
            }
        }
        return $this->execute($sql, $bindArray, "delete");
    }
    
    /**
     * 绑定参数
     * @param Array $bindArray
     */
    public function bind($bindArray){
        if(is_array($bindArray)){
            $this->options["bindArray"] = $bindArray;
        }
    }
    
    /**
     * 读取表的主键
     * @return string 主键名称
     */
    public function findPrimaryKey(){
        $dbname = $this->database;
        $table = $this->table;
        $sql = "select column_name from INFORMATION_SCHEMA.KEY_COLUMN_USAGE where constraint_name='PRIMARY' AND table_name='{$table}' and table_schema='{$dbname}'";
        $row = $this->findFirstSql($sql);
        if($row){
            return $row["column_name"];
        }else{
            return null;
        }
    }
    
    public function table($table){
        $dbConfig = C(self::$_dbname);
        $prefix = "";
        if(isset($dbConfig["DB_PREFIX"])){
            $prefix=$dbConfig["DB_PREFIX"];
        }
        $this->options["table"] = $prefix.$table;
        $this->table = $prefix.$table;
    }
    
    /**
     * 魔术方法
     * @param type $func
     * @param type $args
     * @return \DB
     */
    public function __call($func, $args) {
        if (in_array($func, array('field', 'join', 'where', 'order', 'group', 'limit', 'having'))) {
            $this -> options[$func] = array_shift($args);
            return $this;
        } 
    }
    
    public function begin(){
        $this->link->beginTransaction();
    }
    
    public function commit(){
        $this->link->commit();
    }
    
    public function rollBack(){
        $this->link->rollBack();
    }
    
    //全局执行方法
    public function execute($sql,$bindArray=array(),$action=null){
        $sqlStartTime = getMillisecond();
        $stmt = $this->link->prepare($sql);
        $stmt->execute($bindArray);
        $res = null;
        switch ($action){
            case "select":
                $res = $stmt->fetch(\PDO::FETCH_ASSOC);
                break;
            case "selectAll":
                $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                break;
            case "update":
            case "delete":
                $res = $stmt->rowCount();
                break;
            case "insert":
                $res = $this->link->lastInsertId();
                break;
            case "count":
                $res  = $stmt->rowCount();
                break;
        }
        $this->options = array();
        $sqlEndTime = getMillisecond();
        $showLog = C("OPEN_SQL_LOG");
        if($showLog){
            Log::write(sprintf("SQL COSETIME=【%s】ms,ERRORCODE=【%s】,SQL=【%s】,BIND=【%s】",($sqlEndTime-$sqlStartTime),$this->link->errorCode(), $sql, json_encode($bindArray)));
            if($this->link->errorCode()!="00000"){
                Log::write("SQL ERROR=".json_encode($this->link->errorInfo()),Log::ERR);
            }
        }
        return $res;
    }
    
}

/**
 * 设置当前操作表
 */
function M($table = null, $dbname = "DEFAULT_DB") {
    $db = DB::getInstance($dbname);
    if ($table) {
        $db -> table($table);
    }
    return $db;
}

/**
 * 设置当前操作数据库
 */
function D($dbname = "DEFAULT_DB") {
    $db = DB::getInstance($dbname);
    return $db;
}

