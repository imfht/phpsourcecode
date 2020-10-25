<?php

/**
 * 数据库简化操作
 *
 * @package Comm
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Comm\Db;
class Simple {
    
    /**
     * 数据库操作对象
     * 
     * @var Mysql
     */
    protected $_db;
    
    /**
     * 表名
     * 
     * @var string
     */
    protected $_table;
    
    /**
     * 条件查询
     * 
     * @var String
     */
    protected $_where = '';
    
    /**
     * 查询参数
     * 
     * @var array
     */
    protected $_params = array();
    

    /**
     * 排序方式
     * 
     * @var String
     */
    protected $_order = '';
    
    /**
     * 获取多少条数据
     * 
     * @var String
     */
    protected $_limit = '';
    
    /**
     * 构造方法
     * 
     * @param string $table    要操作的表名(可选，可后期调用table方法重新设置)
     * @param string $db_alias 数据库别名
     */
    public function __construct($table = null, $db_alias = 'database_main') {
        $this->_db = new Mysql($db_alias);
        $table && $this->table($table);
    }
    
    /**
     * 设置要操作的数据表
     * 
     * @param string $table 表名
     * 
     * @return Simple
     */
    public function table($table) {
        $config = $this->_db->showConfig();
        $this->_table = $config->pre . $table;
        return $this;
    }

    /**
     * 获取当前操作的数据表
     * 
     * @return \Comm\Db\string
     */
    public function showTable() {
        return $this->_table;
    }
    
    /**
     * 追加And条件
     * 
     * @param array  $data       条件数据
     * @param string $comparison 比较运算符（= > < LIKE != IN）
     * 
     * @return Simple
     */
    public function wAnd(array $data, $comparison = '=') {
        return $this->_makeSimpleWhere($data, 'AND', $comparison);
    }
    
    /**
     * 追加OR条件
     * 
     * @param array  $data       条件数据
     * @param string $comparison 比较运算符（= > < LIKE != IN）
     * 
     * @return Simple
     */
    public function wOr(array $data, $comparison = '=') {
        return $this->_makeSimpleWhere($data, 'OR', $comparison);
    }
    
    /**
     * 简单拼装运算方法
     * 
     * @param array  $data       数据，Key=>value
     * @param string $condition  条件运算符（AND、OR）
     * @param string $comparison 比较运算符（= > < LIKE != IN）

     * 
     * @return Simple
     */
    protected function _makeSimpleWhere(array $data, $condition, $comparison = '=') {
        $condition = strtoupper($condition);
        $where = array();
        foreach($data as $key => $value) {
            
            if(($comparison === '=' || $comparison === 'IN' ) && is_array($value)) {
                //使用的是IN
                $where[] = "`{$key}` IN (" . self::repeatParams($value). ')';
                $this->_params = array_merge($this->_params, $value);
            } else {
                //其它正常情况
                $where[] = "`{$key}` {$comparison} ?";
                $this->_params[] = $value;
            }
        }
        $where_str = implode(" {$condition} ", $where);
        $this->_where && $this->_where .= " {$condition} ";
        $this->_where .= $where_str;
        return $this;
    }

    
    /**
     * 排序（多次调用为覆盖）
     * 
     * @example $obj->order('create_time', SORT_DESC);
     * @example $obj->order([['create_time', SORT_DESC], ['id', SORT_DESC]]);
     * 
     * @param mixed  $field_or_batch_arr 排序字段名或批量配置
     * @param string $sort               排序方式，枚举常量：SORT_ASC,SORT_DESC
     * 
     * @return Simple
     */
    public function order($field_or_batch_arr, $sort = null) {
        if(is_array($field_or_batch_arr)) {
            $order = '';
            foreach($field_or_batch_arr as $value) {  
                $sort_str = empty($value[1]) ? '' : self::_fetchSortStr($value[1]);
                $order .= "{$value[0]}";
                $sort_str && $order .= " {$sort_str}";
                $order .= ',';
            }
            $order = rtrim($order, ',');
            $this->_order = $order;
        } else {
            $sort_str = self::_fetchSortStr($sort);
            $this->_order = "$field_or_batch_arr";
            $sort_str && $this->_order .= " {$sort_str}";
        }
        return $this;
    }
    
    /**
     * 获取Order排序的字符串（内部静态调用）
     * 
     * @param int $sort 排序常量：SORT_ASC/SORT_DESC
     * 
     * @return string
     */
    protected static function _fetchSortStr($sort) {
        switch($sort) {
            case SORT_ASC :
                $sort_str = 'ASC';
                break;
            case SORT_DESC :
                $sort_str = 'DESC';
                break;
            default :
                $sort_str = '';
                break;
        }
        return $sort_str;
    }
    
    /**
     * 限制获取多少条
     * 
     * @param int $start_or_limit 开始或限制多少
     * @param int $limit          限制多少
     * 
     * @return Simple
     */
    public function limit($start_or_limit, $limit = 0) {
        $start_or_limit = (int)$start_or_limit;
        $limit = (int)$limit;
        $this->_limit = $start_or_limit;
        $limit && $this->_limit .= ",{$limit}";
        return $this;
    }
    
    /**
     * 写入一条数据
     * 
     * @param array   $data           要写入的数据，一维数据，字段=>值
     * @param boolean $ignore         是否为忽略写入（默认否）
     * @param string  $show_row_count 是否需要返回行数（默认不返回）
     * 
     * @return \mixed 若$show_row_count为true，则返加行数，否则无异常返回true
     */
    public function insert(array $data, $ignore = false, $show_row_count = false) {
        $action = 'INSERT';
        $ignore && $action .= ' IGNORE'; 
        $action .= " INTO";
        
        return $this->_simpleWrite($action, $data, $show_row_count);
    }
    
    /**
     * 覆盖一条数据
     *
     * @param array   $data           要写入的数据，一维数据，字段=>值
     * @param string  $show_row_count 是否需要返回行数（默认不返回）
     *
     * @return mixed 若$show_row_count为true，则返加行数，否则无异常返回true
     */
    public function replace(array $data, $show_row_count = false) {
        return $this->_simpleWrite('REPLACE INTO', $data, $show_row_count);
    }
    /**
     * 更新数据
     * 
     * @example $obj->wAnd(['id'=>1])->update('update_time'=>time())
     * 
     * @param array   $data           要更新的数据，一维数据，字段=>值
     * @param string  $show_row_count 是否需要返回行数（默认不返回）
     *
     * @return \mixed 若$show_row_count为true，则返加行数，否则无异常返回true，没有data或条件返回false
     */
    public function upadte(array $data, $show_row_count = false) {
        if(!$data || !$this->_where) {
            return false;
        }
        
        $sql = "UPDATE `{$this->_table}` SET ";
        foreach($data as $key => $value) {
            $sql .= "`{$key}` = '" . addslashes($value) . "',";
        }
        $sql  = rtrim($sql, ',');
        $sql .= " WHERE {$this->_where}";
        $this->_limit && $sql .= " LIMIT {$this->_limit}";
        if($show_row_count) {
            return $this->_db->exec($sql, $this->_params);
        } else {
            $this->_db->executeSql($sql, $this->_params);
            return true;
        }
    }
    
    /**
     * INSERT写入数据，主键冲突时更新
     * 
     * @param array  $data           写入的数据内容
     * @param string $show_row_count 影响行数
     * 
     * @return boolean
     */
    public function iodu(array $data, $show_row_count = false) {
        $sql = "INSERT INTO `{$this->_table}` SET ";
        $sql_value = '';
        foreach($data as $key => $value) {
            $sql_value .= "`{$key}` = :{$key},";
        }
        $sql_value = rtrim($sql_value, ',');
        $sql .= $sql_value;
        $sql .= ' ON DUPLICATE KEY UPDATE ';
        $sql .= $sql_value;
        
        if($show_row_count) {
            return $this->_db->exec($sql, $data);
        } else {
            $this->_db->executeSql($sql, $data);
            return true;
        }
    }
    
    /**
     * 删除数据 
     * 
     * @example $obj->wAnd(['id'=>1])->delete();
     * 
     * @param string  $show_row_count 是否需要返回行数（默认不返回）
     *
     * @return mixed 若$show_row_count为true，则返加行数，否则无异常返回true，没有data或条件返回false
     */
    public function delete($show_row_count = false) {
        $sql = self::fetchSql('DELETE');
        if($show_row_count) {
            return $this->_db->exec($sql, $this->_params);
        } else {
            $this->_db->executeSql($sql, $this->_params);
            return true;
        }
    }
    
    /**
     * 简单的写入操作
     * 
     * @param string  $action         动作
     * @param array   $data           要操作的数据
     * @param string  $show_row_count 是否需要返回行数（默认不返回）
     *
     * @return mixed 若$show_row_count为true，则返加行数，否则无异常返回true，没有data或条件返回false
     */
    protected function _simpleWrite($action, $data, $show_row_count) {
        $sql = "{$action} `{$this->_table}` SET ";
        foreach($data as $key => $value) {
            $sql .= "`{$key}` = :{$key},";
        }
        $sql  = rtrim($sql, ',');
        
        if($show_row_count) {
            return $this->_db->exec($sql, $data);
        } else {
            $this->_db->executeSql($sql, $data);
            return true;
        }
    }
    
    /**
     * 批量写入数据 
     * 
     * @param array   $fields         字段定义
     * @param array   $datas          数据（需要和字段定义个数相同）
     * @param boolean $ignore         是否为忽略写入（默认否）
     * @param string  $show_row_count 是否需要返回行数（默认不返回）
     * 
     * @return mixed 若$show_row_count为true，则返加行数，否则无异常返回true
     */
    public function insertBatch(array $fields, array $datas, $ignore = false, $show_row_count = false) {
        $sql = 'INSERT';
        $ignore && $sql .= ' IGNORE';
        $sql .= " INTO `{$this->_table}` (";
        
        //拼KEY
        foreach($fields as $value) {
            $sql .= "`{$value}`,";
        }
        $sql = rtrim($sql, ',') . ') VALUES';
        
        //接VALUES
        $sql_params = array();
        foreach($datas as $values) {
            $sql .= '(' . self::repeatParams($values) . '),';
            $sql_params = array_merge($sql_params, $values);
        }
        $sql = rtrim($sql, ',');        
        
        if($show_row_count) {
            return $this->_db->exec($sql, $sql_params);
        } else {
            $this->_db->executeSql($sql, $sql_params);
            return true;
        }
    }
    
    /**
     * 获取操作的SQL
     * 
     * @params string $action 要操作的动作（SELECT * | DELETE） 
     * 
     * @return string;
     */
    public function fetchSql($action = 'SELECT *') {
        $sql = "{$action} FROM `{$this->_table}`";
        $this->_where && $sql .= " WHERE {$this->_where}";
        $this->_order && $sql .= " ORDER BY {$this->_order}";
        $this->_limit && $sql .= " LIMIT {$this->_limit}";
        return $sql;
    }
    
    /**
     * 获取全部数据
     * 
     * @example
     * $obj = \Comm\Db::simple('db_alias', 'table');
     * $obj->wAnd(['category_id'=>1])->order('id', SORT_DESC)->limit(0,20)->fetchAll();
     * 
     * @param string  $field      要获取的字段，默认是*
     * @param boolean $use_master 是否强制使用主库
     * 
     * @return \array
     */
    public function fetchAll($field = '*', $use_master = false) {
        $use_master && $this->_db->setWrite();
        $sql = self::fetchSql("SELECT {$field}");
        $result = $this->_db->fetchAll($sql, $this->_params);
        $use_master && $this->_db->setAuto();
        return $result;
    }
    
    /**
     * 获取一列数据
     *
     * @param string $field 要获取的字段，默认是*
     *
     * @return \array
     */
    public function fetchCol($field = '*') {
        $sql = self::fetchSql("SELECT {$field}");
        return $this->_db->fetchCol($sql, $this->_params);
    }
    
    /**
     * 获取一行数据
     *
     * @param string $field 要获取的字段，默认是*
     *
     * @return \array
     */
    public function fetchRow($field = '*') {
        $sql = self::fetchSql("SELECT {$field}");
        return $this->_db->fetchRow($sql, $this->_params);
    }
    
    /**
     * 获取一条数据
     * 
     * @param string $field
     * @param mixed  $index
     * 
     * @return boolean|mixed
     */
    public function fetchOne($field = '*', $index = null) {
        $sql = self::fetchSql("SELECT {$field}");
        return $this->_db->fetchOne($sql, $this->_params, $index);
    }
    
    
    /**
     * 获取最后一次插入产生的ID
     * 
     * @return \int
     */
    public function lastId() {
        return $this->_db->lastId();
    }
    
    /**
     * 清空已设置的对象信息
     * 
     * @return Simple
     */
    public function clean() {
        $this->_where = '';
        $this->_order = '';
        $this->_limit = '';
        return $this;
    }
    
    /**
     * 生成一个数组个数的PDO占位？
     *
     * @param array $data 目标数据
     *
     * @return string
     */
    static public function repeatParams(array $data) {
        return rtrim(str_repeat('?,', count($data)), ',');
    }
    
}
