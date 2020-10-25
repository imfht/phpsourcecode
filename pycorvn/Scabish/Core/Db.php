<?php
namespace Scabish\Core;

use SCS;
use PDO;
use Exception;

/**
 * Scabish\Core\Db
 * 数据库操作类
 * 
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2015-02-02
 */
class Db {
    
    protected $_connect = null;
    protected $_table = null;
    protected $_semantics;
    protected $_cache = 0; // 缓存时间(秒)
    
    const FETCH_COLUMN = PDO::FETCH_COLUMN; // 取出第一列数据
    const FETCH_ASSOC = PDO::FETCH_ASSOC; // 以数组形式返回数据
    const FETCH_OBJ = PDO::FETCH_OBJ; // 以对象形式返回数据
    
    public function __construct($connect = null) {
        $this->_connect = is_null($connect) ? 'default' : $connect;
        $this->_semantics = new \stdClass();
    }
    
    /**
     * 切换数据库连接
     * @param string $connect
     */
    public function Connect($connect) {
        $this->_connect = $connect;
        $this->resetSemantics();
        return $this;
    }
    
    /**
     * 重置SQL语义树
     */
    public function Reset() {
        $this->resetSemantics();
        return $this;
    }
    
    /**
     * 需要查询的字段列表
     * @param string $fields 字段列表
     */
    public function Select($fields = '*') {
        $this->_semantics->field = trim(trim($fields), ',');
        return $this;
    }
    
    /**
     * 选择数据表
     * @param string $table 表名
     * @param string $alias 表别名
     */
    public function From($table, $alias = '') {
        if(preg_match('/^\{.*\}$/s', $table)) { // 表全名 ，如'{SC_Admin}'
            $this->_table = substr($table, 1, -1);
        } else { // 默认自动加表前缀
            $this->_table = SCS::Instance()->db[$this->_connect]['prefix'].$table;
        }
        if($alias) $this->_table .= ' AS '.$alias;
        return $this;
    }
    
    /**
     * 左连接数据表
     * @param string $table 表名
     * @param string $alias 表别名
     * @param string $condition 连接条件
     */
    public function LeftJoin($table, $alias, $condition) {
        if(preg_match('/^\{.*\}$/s', $table)) { // 表全名 ，如'{SC_Admin}'
            $table = substr($table, 1, -1);
        } else { // 默认自动加表前缀
            $table = SCS::Instance()->db[$this->_connect]['prefix'].$table;
        }
        $table .= ' AS '.$alias;
        
        $this->_semantics->leftJoin[] = array($table, $condition);
        return $this;
    }
    
    /**
     * 内连接数据表
     * @param string $table 表名
     * @param string $alias 表别名
     * @param string $condition 连接条件
     */
    public function InnerJoin($table, $alias, $condition) {
        if(preg_match('/^\{.*\}$/s', $table)) { // 表全名 ，如'{SC_Admin}'
            $table = substr($table, 1, -1);
        } else { // 默认自动加表前缀
            $table = SCS::Instance()->db[$this->_connect]['prefix'].$table;
        }
        $table .= ' AS '.$alias;
        $this->_semantics->innerJoin[] = array($table, $condition);
        return $this;
    }
    
    /**
     * SQL WHERE条件
     * @param string $where
     */
    public function Where($where) {
        if(trim($where)) $this->_semantics->where = trim($where);
        return $this;
    }
    
    /**
     * SQL ORDER BY条件
     * @param string $order
     */
    public function Order($order) {
        if(trim($order)) $this->_semantics->order = trim($order);
        return $this;
    }
    
    /**
     * SQL GROUP BY条件
     * @param string $group
     */
    public function Group($group) {
        if(trim($group)) $this->_semantics->group = trim($group);
        return $this;
    }
    
    /**
     * SQL HAVING条件
     * @param string $having
     */
    public function Having($having) {
        if(trim($having)) $this->_semantics->having = trim($having);
        return $this;
    }
    
    /**
     * SQL LIMIT条件
     * @param integer $start
     * @param integer $num
     */
    public function Limit($start, $num = null) {
        if(is_null($num)) {
            if($start) $this->_semantics->limit = $start;
        } else {
            $this->_semantics->limit = $start.', '.$num;
        }
        return $this;
    }
    
    /**
     * 设定参数绑定列表
     * @param array $value : array(':id' => 11, ':name' => 'hello')
     */
    public function BindValue(array $value = null) {
        $this->_semantics->value = $value;
        return $this;
    }
    
    /**
     * 返回单条查询记录
     * 如果仅返回第一列的值，请将$fetchType设置为SCDb::FETCH_COLUMN
     * @example
        $list = SCS::Db()->From('manage_operators', 'o')
        ->LeftJoin('manage_game', 'g', 'o.code = g.code')
        ->Where('o.op_id > :op_id and o.name like :name')
        ->Order('g.game_id DESC')
        ->BindValue([':op_id' => 1, ':name' => '%1%'])
        ->Fetch();
     * @param array $params 需绑定的参数列表 
     * @param const $fetchType 数据返回类型，建议设置为SCDb::FETCH_OBJ或SCDb::FETCH_ASSOC
     * @return array
     * @throws ErrorException
     */
    public function Fetch($fetchType = self::FETCH_OBJ) {
        $sql = 'SELECT ';
        $sql .= isset($this->_semantics->field) ? $this->_semantics->field : '*';
        $sql .= ' FROM '.$this->_table;
        $sql .= $this->jointSql();
        $sql .= ' LIMIT 1';
        
        try {
            return $this->GetCacheResult($sql, $fetchType, false);
        } catch(Exception $e) {
            throw new Exception($e->getMessage().' [SQL:'.$sql.']');
        }
    }
    
    /**
     * 返回多条查询记录
     * 如果仅返回每一行第一列的值，请将$fetchType设置为SCDb::FETCH_COLUMN 
     * @param const $fetchType 数据返回类型，建议设置为SCDb::FETCH_OBJ或SCDb::FETCH_ASSOC
     * @return array
     * @throws ErrorException
     */
    public function FetchAll($fetchType = self::FETCH_OBJ) {
        $sql = 'SELECT ';
        $sql .= isset($this->_semantics->field) ? $this->_semantics->field : '*';
        $sql .= ' FROM '.$this->_table;
        $sql .= $this->jointSql();
        $sql .= isset($this->_semantics->limit) ? ' LIMIT '.$this->_semantics->limit : '';
        
        try {
            return $this->GetCacheResult($sql, $fetchType);
        } catch(Exception $e) {
            throw new Exception($e->getMessage().' [SQL:'.$sql.']');
        }
    }
    
    /**
     *
     * 分页查询数据
     * @param Page $page \Scabish\Core\Page对象 
     * @param const $fetchType 数据返回类型，建议设置为SCDb::FETCH_OBJ或SCDb::FETCH_ASSOC
     * @return array
     * @throws ErrorException
     */
    public function FetchPage(Page $page, $fetchType = self::FETCH_OBJ) {
        $sql = 'SELECT SQL_CALC_FOUND_ROWS ';
        $sql .= isset($this->_semantics->field) ? $this->_semantics->field : '*';
        $sql .= ' FROM '.$this->_table;
        $sql .= $this->jointSql();
        $sql .= ' LIMIT '.($page->current - 1) * $page->size.', '.$page->size;
        
        try {
            $data =  $this->GetCacheResult($sql, $fetchType);
            
            $stmt = $this->dbh()->prepare('SELECT FOUND_ROWS()');
            $stmt->execute();
            
            $page->total = intval($stmt->fetchColumn());
            
            return $data;
        } catch(Exception $e) {
            throw new Exception($e->getMessage().' [SQL:'.$sql.']');
        }
    }
    
    /**
     * 返回单条查询记录(原生SQL查询方式)
     * 如果仅返回第一列的值，请将$fetchType设置为SCDb::FETCH_COLUMN
     * @param string $sql SQL语句 
     * @param const $fetchType 数据返回类型，建议设置为SCDb::FETCH_OBJ或SCDb::FETCH_ASSOC
     * @return array
     * @throws ErrorException
     */
    public function Find($sql, $fetchType = self::FETCH_OBJ) {
        $sql = preg_replace('/\{prefix\}/', SCS::Instance()->db[$this->_connect]['prefix'], $sql);
        try {
            return $this->GetCacheResult($sql, $fetchType, false);
        } catch(Exception $e) {
            throw new Exception($e->getMessage().' [SQL:'.$sql.']');
        }
    }
    
    /**
     * 返回多条查询记录
     * 如果仅返回每一行第一列的值，请将$fetchType设置为SCDb::FETCH_COLUMN
     * @param string $sql SQL语句 
     * @param const $fetchType 数据返回类型，建议设置为SCDb::FETCH_OBJ或SCDb::FETCH_ASSOC
     * @return array
     * @throws ErrorException
     */
    public function FindAll($sql, $fetchType = self::FETCH_OBJ) {
        $sql = preg_replace('/\{prefix\}/', SCS::Instance()->db[$this->_connect]['prefix'], $sql);
        try {
            return $this->GetCacheResult($sql, $fetchType);
        } catch(Exception $e) {
            throw new Exception($e->getMessage().' [SQL:'.$sql.']');
        }
    }
    
    /**
     * 分页查询数据
     * @param string $sql SQL语句
     * @param Page $page \Scabish\Core\Page对象
     * @param const $fetchType 数据返回类型，建议设置为SCDb::FETCH_OBJ或SCDb::FETCH_ASSOC
     * @return array
     * @throws ErrorException
     */
    public function FindPage($sql, Page $page, $fetchType = self::FETCH_OBJ) {
        $sql = preg_replace('/\{prefix\}/', SCS::Instance()->db[$this->_connect]['prefix'], $sql);
        $sql = preg_replace(array('/(?<=SELECT\s)(.*)(?=\sFROM)/is', '/\sLIMIT\s.*/is'), 
                array(' SQL_CALC_FOUND_ROWS \1', ''), $sql); // 换行符的影响
        $sql .= ' LIMIT '.($page->current - 1) * $page->size.', '.$page->size;
        
        try {
            $data =  $this->GetCacheResult($sql, $fetchType);
            
            $stmt = $this->dbh()->prepare('SELECT FOUND_ROWS()');
            $stmt->execute();
            
            $page->total = intval($stmt->fetchColumn());
            
            return $data;
        } catch(Exception $e) {
            throw new Exception($e->getMessage().' [SQL:'.$sql.']');
        }
    }
    
    /**
     * 插入数据
     * @param array $data 一维或二维数组
     * @param boolean $replace 主键重复则替换
     * @return integer 主键ID
     */
    public function Insert($data = null, $replace = false) {
        $attrs = [0 => null];
        if(null === $data) {
            $attrs[0] = SCS::Request()->Post();
        } elseif(is_array($data)) {
            if(!isset($data[0])) {
                $attrs[0] = $data;
            } else {
                $attrs = $data;
            }
        }
        if(!$attrs[0]) throw new Exception('Insert data is empty');
        
        $fields = array();
        foreach($attrs as $a) {
            $fields = array_unique(array_merge($fields, array_keys($a)));
        }
        $table_fields = $this->getTableStruct($this->_table);
        $fields = array_intersect($fields, $table_fields);
        if(in_array('fdCreate', $table_fields) && !in_array('fdCreate', $fields)) {
            $fields[] = 'fdCreate';
        }
        if(in_array('fdUpdate', $table_fields) && !in_array('fdUpdate', $fields)) {
            $fields[] = 'fdUpdate';
        }
        $values = array();
        foreach($attrs as $attr) {
            $value = '';
            foreach($fields as $field) {
                if(array_key_exists($field, $attr) && !is_array($attr[$field]) && !is_object($attr[$field])) {
                    if(preg_match('/^\{.*\}$/s', $attr[$field])) { // 特殊处理标识,如'count' => '{count + 1}'
                        $value .= $this->escape(substr($attr[$field], 1, -1)).', ';
                    } else {
                        $value .= is_null($attr[$field]) ? 'NULL, ' : '"'.$this->escape($attr[$field]).'", ';
                    }
                } else {
                    $value .= in_array($field, ['fdCreate', 'fdUpdate']) ? '"'.date('Y-m-d H:i:s').'", ' : 'NULL, ';
                }
            }
            
            $values[] = '('.rtrim($value, ', ').')';
        }
        
        $sql = ($replace ? 'REPLACE' : 'INSERT').' INTO `'.trim($this->_table, '`').'`';
        $sql .= ' (`'.implode('`, `', $fields).'`)';
        $sql .= ' VALUES '.implode(', ', $values);
        try {
            $stmt = $this->dbh()->prepare($sql);
            if($stmt->execute()) return $this->dbh()->lastInsertId();
        } catch(Exception $e) {
            throw new Exception($e->getMessage().' [SQL:'.$sql.']');
        }
    }
    
    /**
     * 更新数据
     * @param array $data
     * @param boolean $whole 是否整表更新(该参数作用是防止未加WHERE条件引起的灾难性后果)
     * @return integer 更新受影响的记录数
     */
    public function Update($data = null, $whole = false) {
        if(null === $data) $data = SCS::Request()->Post();
        $table_fields = $this->GetTableStruct($this->_table);
        $sets = array();
        foreach($data as $field=>$value) {
            if(!in_array($field, $table_fields)) continue;
            if(is_array($value) || is_object($value)) continue;
            if(preg_match('/^\{.*\}$/s', $value)) { // 特殊处理标识,如'count' => '{count + 1}'
                $sets[] = '`'.$field.'` = '.substr($this->escape($value), 1, -1);
            } else {
                $sets[] = '`'.$field.'` = '.(is_null($value) ? 'NULL' : ('"'.$this->escape($value)).'"');
            }
        }
        if(!in_array('fdUpdate', $data) && in_array('fdUpdate', $table_fields)) {
            $sets[] = '`fdUpdate` = "'.date('Y-m-d H:i:s').'"';
        }
        if(empty($sets)) return 0;
        $sql = 'UPDATE `'.trim($this->_table, '`').'` SET '.implode(', ', $sets);
        if(!isset($this->_semantics->where) && !$whole) { // 不允许整表更新，需强制加条件，防止误更新
            throw new Exception('Update the whole table is not allowed for security [SQL: '.$sql.']');
        } else {
            $sql .= ' WHERE '.$this->_semantics->where;
        }
        
        $sql .= isset($this->_semantics->order) ? ' ORDER BY '.$this->_semantics->order : '';
        $sql .= isset($this->_semantics->limit) ? ' LIMIT '.$this->_semantics->limit : '';
        
        try {
            $stmt = $this->dbh()->prepare($sql);
            $this->boundValue($stmt);
            return $stmt->execute();
        } catch(Exception $e) {
            throw new Exception($e->getMessage().' [SQL:'.$sql.']');
        }
    }
    
    /**
     * 删除数据
     * @param boolean $whole 是否删除整张表(该参数作用是防止未加WHERE条件引起的灾难性后果)
     * @return integer 更新受影响的记录数
     */
    public function Delete($whole = false) {
        $sql = 'DELETE FROM `'.trim($this->_table, '`').'`';
        if(!isset($this->_semantics->where) && !$whole) { // 不允许整表删除，需强制加条件，防止误删
            throw new Exception('Delete the whole table is not allowed for security [SQL: '.$sql.']');
        } else {
            $sql .= isset($this->_semantics->where) ? (' WHERE '.$this->_semantics->where) : '';
        }
        $sql .= isset($this->_semantics->order) ? ' ORDER BY '.$this->_semantics->order : '';
        $sql .= isset($this->_semantics->limit) ? ' LIMIT '.$this->_semantics->limit : '';
        
        try {
            $stmt = $this->dbh()->prepare($sql);
            $this->boundValue($stmt);
            return $stmt->execute();
        } catch(Exception $e) {
            throw new Exception($e->getMessage().' [SQL:'.$sql.']');
        }
    }
    
    /**
     * 执行SQL语句
     * @param string $sql SQL语句
     * @return 受影响记录数
     */
    public function Execute($sql) {
        try {
            $stmt = $this->dbh()->prepare($sql);
            $this->boundValue($stmt);
            return $stmt->execute();
        } catch(Exception $e) {
            throw new Exception($e->getMessage().' [SQL:'.$sql.']');
        }
    }
    
    /**
     * 返回最后一条插入数据的主键ID
     */
    public function LastInsertId() {
        return $this->dbh()->lastInsertId();
    }
    
    /**
     * 开始事务处理
     */
    public function BeginTransaction() {
        $this->dbh()->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
        $this->dbh()->beginTransaction();
    }
    
    /**
     * 撤销事务
     */
    public function RollBack() {
        $this->dbh()->rollBack();
    }
    
    /**
     * 提交事务
     */
    public function Commit() {
        $this->dbh()->commit();
    }
    
    /**
     * 获取表结构信息
     * @param string $table 表名
     * @param string $type fields或pk
     */
    public function GetTableStruct($table, $type = 'fields') {
        if(preg_match('/^\{.*\}$/s', $table)) { // 表全名 ，如'{SC_Admin}'
            $table = substr($table, 1, -1);
        }
        static $structs = array();
        $flag = $this->_connect.'.'.$table;
        if(!isset($structs[$flag])) {
            $stmt = $this->dbh()->prepare('SHOW COLUMNS FROM `'.trim($table, '`').'`');
            $stmt->execute();
            $shows = $stmt->fetchAll(self::FETCH_OBJ);
            $fields = array();
            $pk = null;
            foreach($shows as $k=>$v) {
                $fields[] = $v->Field;
                if('PRI' == $v->Key) $pk = $v->Field;
            }
            $structs[$flag] = compact('pk', 'fields');
        }
        return $type ? $structs[$flag][$type] : $structs[$flag];
    }
    
    /**
     * 设置cache时间
     */
    public function Cache($time) {
        $this->_cache = intval($time);
        return $this;
    }

    /**
     * 获取数据
     * 如果开启缓存则优先从有效缓存中获取数据
     * @param string $sql
     * @param integer $fetchType
     * @param boolean $returnAll
     * @return Ambigous <multitype:, unknown, mixed>
     */
    private function GetCacheResult($sql, $fetchType, $returnAll = true) {
        if(!$this->_cache) return $this->FetchFresh($sql, $fetchType, $returnAll);
        $md5 = md5(strtolower($sql));
        $directory = SC_CACHE_PATH.'/'.substr($md5, 0, 2);
        $file = $directory.'/'.$md5.'.db';
        if(!(file_exists($file) && is_file($file))) {
            mkdir($directory, 0775, true);
            if(false === ($result = touch($file))) {
                throw new Exception('Unable to create cache file: '.realpath($file));
            }
        }
        $stream = unserialize(base64_decode(file_get_contents($file)));
        if(is_array($stream) && isset($stream['time']) && ($stream['time'] + $this->_cache) >= time()) {
            $this->_cache = 0;
            return $stream['data'];
        } else {
            $data = $this->FetchFresh($sql, $fetchType, $returnAll);
            $stream = base64_encode(serialize(['time' => time(), 'data' => $data]));
            if(false === ($result = file_put_contents($file, $stream))) {
                throw new Exception('Unable to write cache file: '.realpath($file));
            }
            $this->_cache = 0;
            return $data;
        }
    }
    
    /**
     * 实时从数据库中获取数据
     * @param string $sql
     * @param integer $fetchType
     * @param boolean $returnAll
     * @return Ambigous <multitype:, unknown, mixed>
     */
    public function FetchFresh($sql, $fetchType, $returnAll) {
        $stmt = $this->Dbh()->prepare($sql);
        $this->boundValue($stmt);
        $stmt->execute();
        if($returnAll) {
            $data = $stmt->fetchAll($fetchType);
            $data =  is_array($data) ? $data : [];
        } else {
            $data = $stmt->fetch($fetchType) ? : false; 
        }
        return $data;
    }
    /**
     * 重置SQL语义构造树
     */
    private function ResetSemantics() {
        $this->_semantics = new \stdClass();
        $this->_table = null;
    }
    
    /**
     * 获取到数据库的连接
     */
    private function Dbh() {
        static $connectors = array();
        if(!isset($connectors[$this->_connect])) {
            $config = SCS::Instance()->db[$this->_connect];
            $connectors[$this->_connect] = new PDO($config['dsn'], $config['username'], $config['password'], array(PDO::ATTR_PERSISTENT => false));
            $connectors[$this->_connect]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 设置异常抛出方式
            $connectors[$this->_connect]->query('SET NAMES utf8'); // 设置数据库查询编码
        }
        
        return $connectors[$this->_connect];
    }
    
    /**
     * 拼接SQL
     * @return string
     */
    private function JointSql() {
        $sql = '';
        if(isset($this->_semantics->leftJoin)) {
            foreach($this->_semantics->leftJoin as $v) {
                $sql .= ' LEFT JOIN '.$v[0].' ON '.$v[1];
            }
        }
        if(isset($this->_semantics->innerJoin)) {
            foreach($this->_semantics->innerJoin as $v) {
                $sql .= ' INNER JOIN '.$v[0].' ON '.$v[1];
            }
        }
        $sql .= isset($this->_semantics->where) ? ' WHERE '.$this->_semantics->where : '';
        $sql .= isset($this->_semantics->group) ? ' GROUP BY '.$this->_semantics->group : '';
        $sql .= isset($this->_semantics->having) ? ' HAVING '.$this->_semantics->having : '';
        $sql .= isset($this->_semantics->order) ? ' ORDER BY '.$this->_semantics->order : '';
        return $sql;
    }
    
    /**
     * 绑定参数值
     * @param object SCDbStatement
     * @param array $params
     */
    private function BoundValue(&$stmt) {
        if(isset($this->_semantics->value) && is_array($this->_semantics->value)) {
            foreach($this->_semantics->value as $k=>$value) {
                $stmt->bindValue(is_int($k) ? $k+1 : $k, $value);
            }
        }
    }
    
    /**
     * 对入库数据进行安全转义
     * @param string $str
     * @return string
     */
    protected function Escape($str) {
        if(get_magic_quotes_gpc()) {
            $str = stripslashes(trim($str));
        }
        return get_magic_quotes_runtime() ? trim($str) : addslashes(trim($str));
    }
}