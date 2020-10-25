<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\dao;

use nb\Access;
use nb\Config;
use nb\Debug;
use PDO;

/**
 * Driver
 *
 * @package nb\dao
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 *
 * @property  PDO db
 */
abstract class Driver extends Access {

    public $object;

    public $prefix = '';//表前戳
    public $name   = '';//不带前戳的表名
    public $alias  = '';//表别名
    public $table  = '';//完整表名

    public $id = null;

    protected $fields = '';

    protected $join = '';

    protected $w = ' WHERE ';
    protected $where = '';

    protected $having = '';
    protected $order = '';
    protected $group = '';

    protected $sql = '';
    protected $params = [];
    protected $distinct = false;

    protected $limit = '';

    protected $data = null;

    protected $server = [];

    protected $exp = [
        'eq'=>'=',
        'neq'=>'<>',
        'gt'=>'>',
        'egt'=>'>=',
        'lt'=>'<',
        'elt'=>'<=',
        'notlike'=>'NOT LIKE',
        'like'=>'LIKE',
        'in'=>'IN',
        'notin'=>'NOT IN',
        'not in'=>'NOT IN',
        'between'=>'BETWEEN',
        'not between'=>'NOT BETWEEN',
        'notbetween'=>'NOT BETWEEN'
    ];

    function __construct($tableName,$id,$server) {

        $this->server = array_merge($this->server,$server);

        if (strchr($tableName, ' ')) {

            $tmp = explode(' ', str_replace(' as ', ' ', $tableName));
            $tableName = $tmp[0];
            $tableAlias = $tmp[1];
        }
        else {
            $tableAlias = $tableName;
        }

        $this->server['object'] and $this->driver->entity($this->server['object']);

        $this->id = $id ? $id : null;

        $this->alias = $tableAlias;
        $this->name = $tableName;
        $this->prefix = $this->server['prefix'];
        $this->table = $this->prefix.$tableName;
    }

    /**
     * 建立一个连接的pdo
     *
     * @return mixed
     */
    abstract protected function _db();

    /**
     * 设置主键
     */
    public function id($pk) {
        $this->id = $pk;
        return $this;
    }

    /**
     * 设置返回的实体类
     * @param bool $class
     */
    function object($class = true) {
        if($class === true) {
            $this->object = 'nb\\Collection';
        }
        else {
            $this->object = $class;
        }
    }

    /**
     * 设置表名
     * @param $tableAlias
     * @return Driver
     */
    function ___table($tableName) {
        $this->name = $tableName;
        $this->table = $this->prefix.$tableName;
        return $this;
    }

    /**
     * 给表设置别名
     * @param $tableAlias
     * @return Driver
     */
    function alias($alias) {
        $this->alias = $alias;
        return $this;
    }

    /**
     * 获取当前数据表名
     * @return String
     */
    function _name(){
        return $this->name;
    }

    /**
     * 获取当前数据带前戳的表名
     * @return String
     */
    function _table(){
        return $this->table;
    }

    /**
     * 获取当前表别名
     * @return String
     */
    function _alias(){
        return $this->alias;
    }

    /**
     * 直接执行完整的SQL
     * @param null $sql
     * @param null $params
     * @return Driver
     */
    function sql($sql = null, $params = NULL) {
        $params = $this->autoarr($params);
        $sql = str_replace('table.',$this->alias.'.',$sql);
        return $this->execute($sql, $params);
        return $this;
    }

    /**
     * 删除表
     */
    public function truncate() {
        $this->execute('TRUNCATE `'.$this->table.'`');
    }

    /**
     * 删除表
     */
    function drop() {
        return $this->execute("DROP TABLE $this->table");
    }

    /**
     * 设置要查询的字段
     * @param $fieldName
     * @return Driver
     */
    public function field($fieldName) {
        if ($this->fields && $this->fields != '*') {
            $fieldName != '*' and $this->fields = $fieldName . ",$this->fields";
        }
        else {
            $this->fields = $fieldName;
        }
        return $this;
    }

    /**
     * 是否去重
     * @param bool $distinct
     * @return Driver
     */
    function distinct($distinct = false) {
        $this->distinct = $distinct;
        return $this;
    }


    private function addJoinField($fields) {
        $this->fields .= $this->fields ? ',' : '';
        $this->fields .= $fields;
        return $this;
    }

    /**
     * @param $table
     * @param string $on
     * @param string $fields
     * @param String $jointype
     * @return Driver
     */
    public function join($table, $on = '', $server=null,$fields = '', $jointype = 'INNER JOIN') {
        $as = $table;
        if (strchr($table, ' ')) {
            $tmp = explode(' ', str_replace(' as ', ' ', $table));
            $table = $tmp[0];
            $as = $tmp[1];
        }
        if ($fields) {
            $this->addJoinField($fields);
        }
        if($server) {
            if(is_string($server)) {
                $server = conf($server);
            }
            $table = $server['dbname'].'.'."`{$server['prefix']}{$table}`";
            if($server['host'] != $this->host) {
                $table = $server['host'].'.'.$table;
            }
        }
        else{
            $table = "`{$this->prefix}{$table}`";
        }
        $on = $on ? 'ON ' . $on : '';
        $this->join .= " $jointype $table $as $on ";
        return $this;
    }

    /**
     * @param $table
     * @param string $on
     * @param string $fields
     * @return Driver
     */
    public function left($table, $on = '', $server=null, $fields = '') {
        return $this->join($table, $on, $server, $fields, 'LEFT JOIN');
    }

    /**
     * @param $table
     * @param string $on
     * @param string $fields
     * @return Driver
     */
    function right($table, $on = '', $server=null,  $fields = '') {
        return $this->join($table, $on, $server, $fields, 'RIGHT JOIN');
    }

    /**
     * @param $table
     * @param string $on
     * @param string $fields
     * @return Driver
     */
    function inner($table, $on = '', $server=null,  $fields = '') {
        return $this->join($table, $on, $server, $fields, 'LEFT JOIN');
    }

    /**
     * @param $condition
     * @param null $params
     * @return Driver
     */
    function where($condition, $params = NULL) {
        if ($condition) {
            if(is_array($condition)) {
                $this->where .= $this->w . $this->parseWhere($condition);
            }
            else {
                $this->where .= $this->w . $condition;
                $params !== NULL && ($this->params = array_merge($this->params,$this->autoarr($params)));
            }
            $this->w = ' and ';
        }
        return $this;
    }

    /**
     * @param $condition
     * @param null $params
     * @return Driver
     */
    function orWhere($condition, $params = NULL) {
        if('WHERE ' != $this->w && $condition){
            $this->w = ' or ';
        }
        return $this->where($condition,$params);
    }



    /**
     * where分析
     * @access protected
     * @param mixed $where
     * @return string
     */
    private function parseWhere($where) {
        $whereStr = '';
        // 使用数组表达式
        $operate  = isset($where['_logic'])?strtoupper($where['_logic']):'';
        if(in_array($operate,['AND','OR','XOR'])){
            // 定义逻辑运算规则 例如 OR XOR AND NOT
            $operate    =   ' '.$operate.' ';
            unset($where['_logic']);
        }
        else{
            // 默认进行 AND 运算
            $operate    =   ' AND ';
        }
        foreach ($where as $key=>$val){
            if(is_numeric($key)){
                $key  = '_complex';
            }
            if(0===strpos($key,'_')) {
                // 解析特殊条件表达式
                $whereStr   .= $this->parseNbWhere($key,$val);
            }
            else{
                // 查询字段的安全过滤
                // if(!preg_match('/^[A-Z_\|\&\-.a-z0-9\(\)\,]+$/',trim($key))){
                //     E(L('_EXPRESS_ERROR_').':'.$key);
                // }
                // 多条件支持
                $multi  = is_array($val) &&  isset($val['_multi']);
                $key    = trim($key);
                if(strpos($key,'|')) { // 支持 name|title|nickname 方式定义查询字段
                    $array =  explode('|',$key);
                    $str   =  [];
                    foreach ($array as $m=>$k){
                        $v =  $multi?$val[$m]:$val;
                        $str[]   = $this->parseWhereItem($this->parseKey($k),$v);
                    }
                    $whereStr .= '( '.implode(' OR ',$str).' )';
                }
                elseif(strpos($key,'&')){
                    $array =  explode('&',$key);
                    $str   =  array();
                    foreach ($array as $m=>$k){
                        $v =  $multi?$val[$m]:$val;
                        $str[]   = '('.$this->parseWhereItem($this->parseKey($k),$v).')';
                    }
                    $whereStr .= '( '.implode(' AND ',$str).' )';
                }
                else{
                    $whereStr .= $this->parseWhereItem($this->parseKey($key),$val);
                }
            }
            $whereStr .= $operate;
        }
        $whereStr = substr($whereStr,0,-strlen($operate));
        return empty($whereStr)?'':' '.$whereStr;
    }

    // where子单元分析
    protected function parseWhereItem($key,$val) {
        $whereStr = '';
        if(is_array($val)) {
            if(is_string($val[0])) {
                $exp	=	strtolower($val[0]);
                if(preg_match('/^(eq|neq|gt|egt|lt|elt)$/',$exp)) { // 比较运算
                    $whereStr .= $key.' '.$this->exp[$exp].' '.$this->parseValue($val[1]);
                }
                elseif(preg_match('/^(notlike|like)$/',$exp)){// 模糊查找
                    if(is_array($val[1])) {
                        $likeLogic  =   isset($val[2])?strtoupper($val[2]):'OR';
                        if(in_array($likeLogic,['AND','OR','XOR'])){
                            $like       =   [];
                            foreach ($val[1] as $item){
                                $like[] = $key.' '.$this->exp[$exp].' '.$this->parseValue($item);
                            }
                            $whereStr .= '('.implode(' '.$likeLogic.' ',$like).')';
                        }
                    }
                    else{
                        $whereStr .= $key.' '.$this->exp[$exp].' '.$this->parseValue($val[1]);
                    }
                }
                elseif('bind' == $exp ){ // 使用表达式
                    $whereStr .= $key.' = :'.$val[1];
                }
                elseif('exp' == $exp ){ // 使用表达式
                    $whereStr .= $key.' '.$val[1];
                }
                elseif(preg_match('/^(notin|not in|in)$/',$exp)){ // IN 运算
                    if(isset($val[2]) && 'exp'==$val[2]) {
                        $whereStr .= $key.' '.$this->exp[$exp].' '.$val[1];
                    }
                    else{
                        if(is_string($val[1])) {
                            $val[1] =  explode(',',$val[1]);
                        }
                        $zone      =   implode(',',$this->parseValue($val[1]));
                        $whereStr .= $key.' '.$this->exp[$exp].' ('.$zone.')';
                    }
                }
                elseif(preg_match('/^(notbetween|not between|between)$/',$exp)){ // BETWEEN运算
                    $data = is_string($val[1])? explode(',',$val[1]):$val[1];
                    $whereStr .=  $key.' '.$this->exp[$exp].' '.$this->parseValue($data[0]).' AND '.$this->parseValue($data[1]);
                }
                else{
                    E(L('_EXPRESS_ERROR_').':'.$val[0]);
                }
            }
            else {
                $count = count($val);
                $rule  = isset($val[$count-1]) ? (is_array($val[$count-1]) ? strtoupper($val[$count-1][0]) : strtoupper($val[$count-1]) ) : '' ;
                if(in_array($rule,['AND','OR','XOR'])) {
                    $count  = $count -1;
                }
                else{
                    $rule   = 'AND';
                }
                for($i=0;$i<$count;$i++) {
                    $data = is_array($val[$i])?$val[$i][1]:$val[$i];
                    if('exp'==strtolower($val[$i][0])) {
                        $whereStr .= $key.' '.$data.' '.$rule.' ';
                    }
                    else{
                        $whereStr .= $this->parseWhereItem($key,$val[$i]).' '.$rule.' ';
                    }
                }
                $whereStr = '( '.substr($whereStr,0,-4).' )';
            }
        }
        else {
            //对字符串类型字段采用模糊匹配
            $likeFields   =   $this->config['db_like_fields'];
            if($likeFields && preg_match('/^('.$likeFields.')$/i',$key)) {
                $whereStr .= $key.' LIKE '.$this->parseValue('%'.$val.'%');
            }
            else {
                $whereStr .= $key.' = '.$this->parseValue($val);
            }
        }
        return $whereStr;
    }

    /**
     * 特殊条件分析
     * @access protected
     * @param string $key
     * @param mixed $val
     * @return string
     */
    protected function parseNbWhere($key,$val) {
        $whereStr   = '';
        switch($key) {
            case '_string':
                // 字符串模式查询条件
                $whereStr = $val;
                break;
            case '_complex':
                // 复合查询条件
                $whereStr = substr($this->parseWhere($val),6);
                break;
            case '_query':
                // 字符串模式查询条件
                parse_str($val,$where);
                if(isset($where['_logic'])) {
                    $op   =  ' '.strtoupper($where['_logic']).' ';
                    unset($where['_logic']);
                }
                else{
                    $op   =  ' AND ';
                }
                $array   =  [];
                foreach ($where as $field=>$data) {
                    $array[] = $this->parseKey($field).' = '.$this->parseValue($data);
                }
                $whereStr   = implode($op,$array);
                break;
        }
        return '( '.$whereStr.' )';
    }

    /**
     * 字段名分析
     * @access protected
     * @param string $key
     * @return string
     */
    protected function parseKey(&$key) {
        return $key;
    }

    /**
     * value分析
     *
     * @access protected
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value) {
        if(is_string($value)) {
            $value =  strpos($value,':') === 0 && in_array($value,array_keys($this->bind))? $this->escapeString($value) : '\''.$this->escapeString($value).'\'';
        }
        elseif(isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp'){
            $value =  $this->escapeString($value[1]);
        }
        elseif(is_array($value)) {
            $value =  array_map([$this, 'parseValue'],$value);
        }
        elseif(is_bool($value)){
            $value =  $value ? '1' : '0';
        }
        elseif(is_null($value)){
            $value =  'null';
        }
        return $value;
    }

    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str  SQL字符串
     * @return string
     */
    public function escapeString($str) {
        return addslashes($str);
    }


    /**
     * 拼接in条件
     * @param $field 要过滤的字段
     * @param $filter 排除条件
     * @param string $not true/fasle
     * 		eg: in('id',[1,2])  ==> id in(1,2)
     * 			in('name',"'aa','bb','cc'")  ==> id in('aa','bb','cc')
     * 			in('id',[1,2,3],true)  ==> id not in(1,2,3)
     * @return Driver
     */
    function in($field, $filter,$not = false) {
        if(!$filter) return $this;
        $not = $not?'NOT':'';
        if(is_array($filter)){
            $comma = '';
            $setFields = '';
            foreach ($filter as $key => $value) {
                $params[] = $value;
                $setFields .= "{$comma}?";
                $comma = ',';
            }
            $this->where .= $this->w . $field.' '.$not.' in('.$setFields.')';
            $this->params = array_merge($this->params,$params);
        }
        else {
            $this->where .= $this->w . $field.' in('.$filter.')';
        }
        $this->w = ' AND ';
        return $this;
    }

    function orIn($field, $filter,$not = false) {
        $this->w = ' OR ';
        $this->in($field, $filter,$not);
        return $this;
    }

    /**
     * @param $field
     * @param $min
     * @param null $max
     * @param bool $not
     * eg: between('id',1,2)  ==> id between ? and ?, 1,2)
     * 	   between('id','1 and 2')  ==> id between 1 and 2
     * 	   between('id',1,2,true)  ==> id not between ? and ?, 1,2)
     *     between('id','1 and 2',null,true)  ==> id not between 1 and 2
     *
     * @return $this
     */
    function between($field,$min,$max=null,$not=false) {
        $not = $not?'NOT':'';
        if($max !== null) {
            $this->where .= $this->w . $field.' '.$not.' BETWEEN ? AND ?';
            $this->params = array_merge($this->params,[
                $min,$max
            ]);
        }
        else {
            $this->where .= $this->w . $field.' '.$not.' BETWEEN '.$min;
        }
        $this->w = ' AND ';
        return $this;
    }

    function orBetween($field,$min,$max=null,$not=false) {
        $this->w = ' OR ';
        $this->between($field,$min,$max,$not);
        return $this;
    }

    /**
     * 拼接like条件
     * @param $field 要过滤的字段
     * @param $filter 排除条件
     * @param string $rl
     * 		eg: like('name','hello') ==  name like 'helo'
     * 			like('name','hello','l') ==  name like '%helo'
     * 			like('name','hello','r') ==  name like 'helo%'
     * 			like('name','hello','a') ==  name like '%helo%'
     * @return Driver
     */
    function like($field, $filter,$rl='a',$not = false) {
        $not = $not?'NOT':'';
        switch($rl) {
            case 'n':
            default:
                $this->where .= "$this->w $field $not like ?";
                $this->params[] = "$filter";
                break;
            case 'a':
                $this->where .= "$this->w $field $not like ?";
                $this->params[] = "%$filter%";
                break;
            case 'l':
                $this->where .= "$this->w $field $not like ?";
                $this->params[] = "%$filter";
                break;
            case 'r':
                $this->where .= "$this->w $field $not like ?";
                $this->params[] = "$filter%";
                break;
        }
        $this->w = ' AND ';
        return $this;
    }

    function orLike($field, $filter,$rl='a',$not = false) {
        $this->w = ' OR ';
        $this->like($field, $filter, $rl, $not);
        return $this;
    }


    /**
     * @param $condition
     * @param null $params
     * @return Driver
     */
    function having($condition, $params = NULL) {
        $this->having = 'HAVING ' . $condition;
        $params && ($this->params = array_merge($this->params, $this->autoarr($params)));
        return $this;
    }

    /**
     * @param $order
     * @return Driver
     */
    function orderby($order) {
        $this->order = $order;
        return $this;
    }

    /**
     * @param $group
     * @return Driver
     */
    function groupby($group) {
        $this->group = $group;
        return $this;
    }

    /**
     * 分页函数
     * @param int $rows 每页大小
     * @param int $start 页数，从1开始
     * @return Driver
     */
    function limit($rows = 0, $start = 1) {
        if($rows > 0) {
            $start>0 and $start--;
            $this->limit = 'LIMIT '.($rows*$start).','.$rows;
        }
        return $this;
    }

    /**
     * 构建sql
     * @param bool $return
     * @return sql|string
     */
    private function _constructSql() {
        if($this->sql) {
            return [$this->sql,$this->params];
        }
        $distinct = $this->distinct ? 'DISTINCT' : '';
        $groupby = '';
        if (!empty($this->group)) {
            $groupby = 'GROUP BY ' . $this->group;
        }
        if (!empty($this->having)) {
            $groupby .= ' ' . $this->having;
        }
        $order = !empty($this->order) ? "ORDER BY $this->order" : '';

        $this->fields = $this->fields?$this->fields:'*';
        $sql = "SELECT $distinct $this->fields FROM `$this->table` $this->alias $this->join $this->where $groupby $order $this->limit";
        $sql = str_replace('table.',$this->alias.'.',$sql);
        return [$sql,$this->params];
    }


    /**
     * 执行一条SQL语句并返回一个statement对象
     * @param Array /String $params
     * @return \PDOStatement query result
     */
    function query() {
        if(empty($this->sql)) {
            $this->_constructSql();
        }
        list($sql, $param) = $this->_constructSql();
        $this->_reset();
        return $this->execute($sql, $param);
    }

    /**
     * 获取一条结果
     * NPdo::FETCH_ASSOC：指定获取方式，将对应结果集中的每一行作为一个由列名索引的数组返回。
     * 如果结果集中包含多个名称相同的列，则NPdo::FETCH_ASSOC每个列名只返回一个值
     * @param string $multi_call_params
     * @param int $fetchMode
     */
    function fetch($fetchMode = PDO::FETCH_ASSOC) {
        return $this->_data($this->query()->fetch($fetchMode),false);
        //return $this->query()->fetch($fetchMode);
    }



    /**
     * 获取一条结果,以对象的形式返回
     * NPdo::FETCH_ASSOC：指定获取方式，将对应结果集中的每一行作为一个由列名索引的数组返回。
     * 如果结果集中包含多个名称相同的列，则NPdo::FETCH_ASSOC每个列名只返回一个值
     * @param string $multi_call_params
     * @param int $fetchMode
     */
    function fetchObject() {
        return $this->query()->fetchObject();
    }

    /**
     * 获取多条条结果
     * PDO::FETCH_ASSOC：指定获取方式，将对应结果集中的每一行作为一个由列名索引的数组返回。
     * 如果结果集中包含多个名称相同的列，则PDO::FETCH_ASSOC每个列名只返回一个值
     * @param string $multi_call_params
     * @param int $fetchMode
     */
    function fetchAll($fetchMode = PDO::FETCH_ASSOC) {
        return $this->_data($this->query()->fetchAll($fetchMode),true);
    }

    /**
     * 获取结果集和数量
     * @return [int,array]
     */
    public function paginate($fetchMode = PDO::FETCH_ASSOC) {
        $distinct = $this->distinct ? 'DISTINCT' : '';
        $groupby = '';
        if (!empty($this->group)) {
            $groupby = 'GROUP BY ' . $this->group;
            if (!empty($this->having)) $groupby .= ' ' . $this->having;
        }

        //先获取数量
        $sql = "SELECT $distinct count('*') FROM `$this->table` $this->alias $this->join $this->where $groupby";
        $sql = str_replace('table.',$this->alias.'.',$sql);
        $num = $this->execute($sql, $this->params)->fetchColumn();

        //再获取数据
        $order = !empty($this->order) ? "ORDER BY $this->order" : '';

        $this->fields = $this->fields?$this->fields:'*';
        $sql = "SELECT $distinct $this->fields FROM `$this->table` $this->alias $this->join $this->where $groupby $order $this->limit";
        $sql = str_replace('table.',$this->alias.'.',$sql);

        $result = $this->execute($sql, $this->params)->fetchAll($fetchMode);
        $result = $this->_data($result,true);

        return [$num,$result];
    }

    /**
     *
     * PDO::FETCH_UNIQUE:只取唯一值
     * PDO::FETCH_COLUMN:指定获取方式，从结果集中的下一行返回所需要的那一列。
     * @param string $multi_call_params
     */
    function fetchAllUnique() {
        return $this->query()->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE, 0);
    }

    /**
     * 获取一条数据的第一个字段
     * @param string $multi_call_params
     */
    function fetchColumn() {
        return $this->query()->fetchColumn();
    }

    /**
     * 指定某个字段为键值组成新的数组返回
     * @param null $filed 当是连表查询时，需要指定此字段
     *          $db->field('f.id,f.src');
     *          $db->fetchKv('id,src');
     *
     * @return array|bool
     */
    function kv($map=null){
        $filed = $this->fields;
        $map = $map?:$filed;
        $filed = explode(',',$map);
        switch(count($filed)) {
            case 0:
            case 1:
                !$filed[0] and new \Exception('fetchKv cat`t fields is null');
                $k = null;//$filed[0];
                $v = $filed[0];
                $this->fields = $v;//'*';
                break;
            case 2:
                $k = $filed[0];
                if($filed[1]=='*') {
                    $v = null;
                    $this->fields = '*';
                }
                else {
                    $v = $filed[1];
                }
                break;
            default:
                $k = $filed[0];
                $v = null;
                $this->fields = $map;
                break;
        }
        $result = $this->query()->fetchAll(PDO::FETCH_ASSOC);

        if($result) {
            $result = array_column($result, $v, $k);
            $result = $this->_data($result,false);
        }
        return $result;
    }

    /**
     * 获取一条数据，以数字索引的方式返回
     * PDO::FETCH_NUM:指定获取方式，将对应结果集中的每一行作为一个由列号索引的数组返回，从第 0 列开始。
     * @param string $multi_call_params
     */
    function fetchIndexed() {
        return $this->fetch(PDO::FETCH_NUM);
    }

    /**
     * 获取去重后的结果集数量
     * @param string $distinctFields
     */
    function count($distinctFields = '') {
        $this->fields = $distinctFields ? "count(DISTINCT $distinctFields)" : 'count(*)';
        return $this->fetchColumn();
    }

    /**
     * 插入一条数据
     * @param unknown $arr
     * @return boolean
     */
    function insert(array $arr) {
        if (empty($arr)) return false;
        $comma = '';
        $fileds = '';
        $prepare='';
        foreach ($arr as $key => $value) {
            $params[] = $value;
            $fileds .= "$comma `$key`";
            $prepare .= "$comma ?";
            $comma = ',';
        }

        $sql = "INSERT INTO  `$this->table`({$fileds}) VALUES({$prepare})";
        $this->execute($sql, $params);
        return $this->db->lastInsertId($this->id);
        /*
         * 此方式不支持sqlite,舍弃
        if (empty($arr)) return false;
        $comma = '';
        $setFields = '';
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $setFields .= "{$comma} `{$key}`=" . current($value);
            }
            else {
                $params[] = $value;
                $setFields .= "$comma `$key`=?";
            }
            $comma = ',';
        }

        $sql = "INSERT INTO  `$this->table` set {$setFields}";
        $sql = str_replace('table.',$this->table.'.',$sql);
        $this->db->sql($sql, $params);
        return $this->db->lastInsertId($this->id);
        */
    }

    /**
     * 批量添加
     * @param array $arr
     * @param array $fieldNames
     * @return boolean
     */
    public function batchInsert($arr, $fieldNames = []) {
        if (empty($arr)) return false;
        if (!empty($fieldNames)) {
            $keys = is_array($fieldNames) ? implode(',', $fieldNames) : $fieldNames;
        }
        else {
            $keys = implode(',', array_keys($arr[0]));
        }
        $sql = 'INSERT INTO ' . $this->table . " ({$keys}) VALUES ";
        $comma = '';
        $params = [];
        foreach ($arr as $a) {
            $sql .= $comma . '(';
            $comma2 = '';
            foreach ($a as $v) {
                $sql .= $comma2 . '?';
                $params[] = $v;
                $comma2 = ',';
            }
            $sql .= ')';
            $comma = ',';
        }
        $sql = str_replace('table.',$this->table.'.',$sql);
        return $this->execute($sql, $params,false);
    }


    /**
     * 更新数据
     * @param array $arr
     * @param string &array $condition
     * @return boolean
     */
    function update($data,$param=[]) {
        if (empty($data)) return false;
        $setFields = '';
        $params = [];
        if (is_array($data)) {
            $comma = '';
            foreach ($data as $key => $value) {
                //add database real string
                if (is_array($value)) {
                    $setFields .= "{$comma} `{$key}`=" . current($value);
                }
                else {
                    $params[] = $value;
                    $setFields .= "{$comma} `{$key}`=?";
                }
                $comma = ',';
            }
        }
        else {
            $params = is_array($param)?$param:[$param];
            $setFields = $data;
        }
        $sql = "UPDATE `{$this->table}` set {$setFields}";
        if($this->where) {
            $sql .= ' '.$this->where;
            $params = array_merge($params,$this->params);
            //$params = array_merge($params, $this->autoarr($condition[1]));
        }
        $sql = str_replace('table.',$this->table.'.',$sql);
        $this->_reset();
        return $this->execute($sql, $params, false);
    }

    function batchUpdate($values, $index, $condition = null,$param=[]) {
        $ids = [];

        foreach ($values as $key => $val) {
            $ids[] = $val[$index];

            foreach (array_keys($val) as $field) {
                if ($field != $index) {
                    $final[$field][] = 'WHEN ' . $index . ' = ' . $val[$index] . ' THEN ' . $val[$field];
                }
            }
        }

        $sql = "UPDATE `$this->table` SET ";
        $cases = '';

        foreach ($final as $k => $v) {
            $cases .= $k . ' = CASE ' . "\n";
            foreach ($v as $row) {
                $cases .= $row . "\n";
            }

            $cases .= 'ELSE ' . $k . ' END, ';
        }

        $sql .= substr($cases, 0, -2);

        $where = ' WHERE ';
        $params = [];
        if (!empty($condition)) {
            if (is_array($condition)) {
                if(is_array($condition[0])) {
                    $where .=  $this->parseWhere($condition[0]);
                }
                else {
                    $where .= $condition[0];
                    $params = array_merge($params, $this->autoarr($condition[1]));
                }
            }
            else {
                $params = array_merge($params, $this->autoarr($param));
                $where .= ' WHERE ' . $condition;
            }
            $where .= ' AND ';
        }

        //$where = ($where != '' AND count($where) >= 1) ? implode(" ", $where) . ' AND ' : '';
        $sql .= $where.$index . ' IN (' . implode(',', $ids) . ')';
        $this->_reset();
        return $this->execute($sql, $params, false);
    }

    /**
     * 删除一条数据
     * @param string $condition
     * @param array $params
     */
    function delete($condition = '', $params = []) {
        $sql = "DELETE FROM `$this->table`";

        if($this->where) {
            $sql .= ' '.$this->where;
        }

        if ($condition) {
            if(is_array($condition)) {
                $sql .= $this->w . $this->parseWhere($condition[0]);
            }
            elseif ($params) {
                //using prepared statement.
                if (!is_array($params)) {
                    $params = [$params];
                }
                $sql .= $this->w . $condition;
                $this->params = array_merge($this->params, $params);
            }
            else {
                $sql .= $this->w . $condition;
            }
        }

        $sql = str_replace('table.',$this->table.'.',$sql);
        $params = $this->params;

        $this->_reset();

        return $this->execute($sql, $params,false);
    }



    private function _reset() {
        $this->fields = '*';
        $this->join = '';
        $this->where = '';
        $this->having = '';
        $this->order = '';
        $this->group = '';
        $this->distinct = false;
        $this->limit = '';
        $this->w = ' WHERE ';
        $this->sql = '';
        $this->params = [];
    }

    protected function _data($data) {
        if($this->object) {
            $object = $this->object;
            return new $object(is_array($data)?$data:[]);
        }
        return $data;
        /*
        if($data && is_array($data) && $this->object) {
            $object = $this->object;
            return new $object($data);
        }
        return $data;
        */
    }

    /**
     * @param $sql
     * @param null $params
     * @param bool $isselect 是否是查询语句
     * @return int|\PDOStatement
     * @throws \Exception
     */
    public function execute($sql, $params = NULL,$isselect=true) {

        \nb\Debug::record(3, $sql, $params);

        $db = $this->db->prepare($sql);

        $result = is_null($params) ? $db->execute() : $db->execute($params);
        if (false !== $result) {
            return $isselect?$db:$db->rowCount();
        }
        $error = $db->errorInfo();
        if(isset($error[2])) {
            $error = "[{$error[0]}][{$error[1]}]{$error[2]}";
        }
        else {
            $error = $sql.': '.json_encode($params);
        }

        throw new \Exception($error);
    }

    private function autoarr($params) {
        if (!is_null($params) && !is_array($params)) {
            $params = [$params];
        }
        return $params;
    }

}
