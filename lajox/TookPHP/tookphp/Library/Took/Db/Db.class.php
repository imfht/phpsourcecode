<?php
/**
 * Mysql数据库基类
 * @package     Db
 * @subpackage  Driver
 * @author      lajox <lajox@19www.com>
 */
namespace Took\Db;
abstract class Db implements DbInterface
{

    public $table = NULL; //表名
    public $pri = null; //默认表主键
    public $tablePrefix = null; //默认表前缀
    public $fieldData; //字段数组
    public $lastQuery; //最后发送的查询结果集
    public $opt = array(
        'table' => null,
        'pri' => null,
        'field' => '*',
        'fieldData' => array(),
        'where' => '',
        'like' => '',
        'group' => '',
        'having' => '',
        'order' => '',
        'limit' => '',
        'join' => array(),
        'union' => '',
        'distinct' => '',
        'lock' => '',
        'comment' => '',
        'force' => '',
        'cacheTime' => null//查询缓存时间
    ); //SQL 操作
    // 查询表达式
    protected $selectSql  = 'SELECT%DISTINCT% %FIELD% FROM %TABLE%%FORCE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT% %UNION%%LOCK%%COMMENT%';
    public $lastSql; //最后发送的SQL
    public $error = NULL; //错误信息
    protected $querySql   = ''; //当前SQL指令
    //链操作方法列表
    protected $methods = array('strict','order','alias','having','group','lock','distinct','index','force');

    /**
     * 将eq等替换为标准的SQL语法
     * @var  array
     */
    protected $condition = array(
        "eq" => " = ", "neq" => " <> ",
        "gt" => " > ", "egt" => " >= ",
        "lt" => " < ", "elt" => " <= ",
    );

    /**
     * 构造函数
     */
    public function __construct($prefix = null){
        if(is_null($prefix)) {
            $this->tablePrefix = C('DB_PREFIX');
        }
        else {
            $this->tablePrefix = $prefix;
        }
    }

    /**
     * 魔术方法用于动态执行Db类中的方法
     *
     * @param $method
     * @param $param
     * @return mixed
     */
    public function __call($method, $param)
    {
        if(in_array(strtolower($method),$this->methods,true)) {
            // 连贯操作的实现
            $this->opt[strtolower($method)] = $param[0];
        }else{
            halt(__CLASS__.':'.$method."方法不存在！");
            return;
        }
    }

    /**
     * 数据库连接
     * 根据配置文件获得数据库连接对象
     * @param string $table
     * @param string $prefix 表前缀
     * @return Object 连接对象
     */
    public function link($table, $prefix = null)
    {
        $args = func_get_args();
        if(isset($args[1])) {
            if(is_null($prefix)) {
                $this->tablePrefix = C('DB_PREFIX');
            }
            else {
                $this->tablePrefix = empty($prefix) ? '' : $prefix;
            }
        }
        //通过数据驱动如MYSQLI连接数据库
        if ($this->connect()) {
            if ($table) {
                //初始化表
                $this->table($table, '');
                //数据表
                $this->table = $this->opt['table'];
                //表字段数据
                $this->fieldData = $this->opt['fieldData'];
                //表主键
                $this->pri = $this->opt['pri'];
            }
            return $this->link;
        } else {
            $this->error('数据库连接错误');
        }
    }

    /**
     * 初始化表字段与主键
     * @param string $table
     * @param string $prefix 表前缀
     * @return void
     */
    public function table($table, $prefix = '')
    {
        //初始化opt参数
        $this->optInit();
        //字段集
        $fieldData = $this->getAllField($table, $prefix);
        //表主键
        $pri = $this->getPrimaryKey($table, $prefix);
        //设置选项
        $this->opt['table'] = $this->_getTable($table, $prefix);
        $this->opt['fieldData'] = $fieldData;
        $this->opt['pri'] = $pri;
    }

    /**
     * 获得表结构及主键
     * 查询表结构获得所有字段信息，用于字段缓存
     * @access private
     * @param string $table
     * @param string $prefix 表前缀
     * @return array
     */
    public function getAllField($table, $prefix = '')
    {
        static $tableData = array();
        $table = preg_replace('@\s+@', ' ', $table);
        list($table) = explode(' ', $table);
        if(strpos($table, '.')){
            list($db, $table) = explode('.', $table);
            $name = $db . '.' . $table;
            $sql   = 'SHOW COLUMNS FROM `'.$db.'`.`'.$table.'`';
        }else{
            $table = $this->_getTable($table, $prefix);
            $name = C('DB_DATABASE') . '.' . $table;
            $sql   = 'SHOW COLUMNS FROM `'.$table.'`';
        }
        $name = strtolower($name);
        //字段缓存
        if (!DEBUG && F($name)) {
            $fieldData = F($name);
        } else {
            $fieldData = array();
            if(isset($tableData[$name]) && $tableData[$name]) {
                $fieldData = $tableData[$name];
            }
            else {
                if (!$result = $this->query($sql)) {
                    return false;
                }
                foreach ($result as $res) {
                    $f ['field'] = $res ['Field'];
                    $f ['type'] = $res ['Type'];
                    $f ['null'] = $res ['Null'];
                    $f ['field'] = $res ['Field'];
                    $f ['key'] = ($res ['Key'] == "PRI" && $res['Extra']) || $res ['Key'] == "PRI";
                    $f ['default'] = $res ['Default'];
                    $f ['extra'] = $res ['Extra'];
                    $fieldData [$res ['Field']] = $f;
                }
            }
            $tableData[$name] = $fieldData;
            DEBUG || F($name, $fieldData, TEMP_TABLE_PATH);
        }
        return $fieldData;
    }

    /**
     * 获得表主键字段
     * @param string $table 数据表
     * @param string $prefix 表前缀
     * @return string
     */
    private function getPrimaryKey($table, $prefix = '')
    {
        $fieldData = $this->getAllField($table, $prefix);
        $pri = '';
        foreach ($fieldData as $field => $v) {
            if ($v['key'] == 1) {
                $pri = $field;
            }
        }
        return $pri;
    }

    /**
     * 查询初始化
     * @access public
     * @return array
     */
    protected function optInit()
    {
        $this->cacheTime = -1; //SELECT查询缓存时间
        $this->error = NULL;
        $opt = array(
            'table' => $this->table,
            'pri' => $this->pri,
            'field' => '*',
            'fieldData' => $this->fieldData,
            'where' => '',
            'like' => '',
            'group' => '',
            'having' => '',
            'order' => '',
            'limit' => '',
            'join' => array(),
            'union' => '',
            'distinct' => '',
            'lock' => '',
            'comment' => '',
            'force' => '',
            'cacheTime' => null,//查询缓存时间
        );
        return $this->opt = array_merge($this->opt, $opt);
    }

    /**
     * 查找满足条件的所有记录(一维数组)
     * 示例：$Db->getField("username")
     */
    public function getField($field, $returnAll = false)
    {
        //设置查询字段
        $this->field($field);
        $result = $this->select();
        if ($result) {
            //字段数组
            $field = explode(',', str_replace(' ', '', $field));
            //如果有多个字段时，返回多维数组并且第一个字段值做为KEY使用
            switch (count($field)) {
                case 1:
                    //只有一个字段，只返回一个字段值
                    if ($returnAll) {
                        $data = array();
                        foreach ($result as $v) {
                            $data[] = current($v);
                        }
                        return $data;
                    } else {
                        return current($result[0]);
                    }
                case 2:
                    $data = array();
                    foreach ($result as $v) {
                        $data[$v[$field[0]]] = $v[$field[1]];
                    }
                    return $data;
                default:
                    $data = array();
                    foreach ($result as $v) {
                        $data[$v[$field[0]]] = $v;
                    }
                    return $data;
            }
        } else {
            return array();
        }
    }

    /**
     * 查找记录
     * @param string $options
     * @return array
     */
    public function select($options = '')
    {
        //有查询条件时
        $this->where($options);
        $sql = $this->buildSelectSql();
        return $this->query($sql);
    }

    /**
     * SQL中的REPLACE方法，如果存在与插入记录相同的主键或unique字段进行更新操作
     * @param array $data 添加数据
     * @param string $type INSERT REPLACE
     * @return array|bool
     */
    public function insert($data = array(), $type = 'INSERT')
    {
        $value = $this->formatField($data);
        //更新数据不能为空
        if (!$value) {
            $this->optInit();
            return false;
        }
        $sql = $type . " INTO " . $this->parseTable( $this->opt['table'] ) . "(" . implode(',', $value['fields']) . ")" .
            "VALUES (" . implode(',', $value['values']) . ")";
        return $this->exec($sql);
    }

    /**
     * REPLACE更新表
     * @param $data
     * @return array|bool
     */
    public function replace($data)
    {
        return $this->insert($data, 'REPLACE');
    }

    /**
     * 更新数据
     * @access      public
     * @param  mixed $data
     * @return mixed
     */
    public function update($data = array())
    {
        //更改必须有条件。如果更新数据中有主键,则以主键做为条件使用
        if (empty($this->opt['where'])) {
            if (isset($data[$this->opt['pri']])) {
                $this->opt['where'] = "" . $this->opt['pri'] . " = " . intval($data[$this->opt['pri']]);
            } else {
                return false;
            }
        }
        $data = !empty($data) ? $data : $_POST;
        //检测更新数据字段合法性, 与字段安全处理
        $data = $this->formatField($data);
        //没有更新数据
        if (empty($data)) {
            $this->optInit();
            return false;
        }
        $sql = "UPDATE " . $this->parseTable( $this->opt['table'] ) . " SET ";
        foreach ($data['fields'] as $n => $field) {
            $sql .= $field . "=" . $data['values'][$n] . ',';
        }
        $sql = trim($sql, ',') . $this->parseWhere( $this->opt['where'] ) . $this->parseLimit($this->opt['limit']);
        return $this->exec($sql);
    }

    /**
     * 删除数据
     * @param string $where 删除条件
     * @return bool
     */
    public function delete($where = '')
    {
        $this->where($where);
        if (empty($this->opt['where'])) {
            $this->optInit();
            return false;
        }
        $sql = "DELETE FROM " . $this->parseTable( $this->opt['table'] ) . $this->parseWhere( $this->opt['where'] ) . $this->parseLimit($this->opt['limit']);
        return $this->exec($sql);
    }

    /**
     * 格式化SQL操作参数 字段加上标识符  值进行转义处理
     * @param array $vars 处理的数据
     * @return array
     */
    public function formatField($vars)
    {
        //格式化的数据
        $data = array();
        foreach ($vars as $k => $v) {
            //校验字段与数据
            if ($this->isField($k)) {
                $data['fields'][] = "`" . $k . "`";
                $v = $this->escapeString($v);
                $data['values'][] = is_numeric($v) ? $v : "\"$v\"";
            }
        }
        return $data;
    }

    /**
     * 检测是否为表字段
     * @param string $field 字段名
     * @return bool
     */
    public function isField($field)
    {
        return isset($this->opt['fieldData'][$field]);
    }

    /**
     * SQL查询条件
     * @param mixed $opt 链式操作中的WHERE参数
     * @return string
     */
    public function where($opt)
    {
        $where = '';
        //没有查询条件
        if (empty($opt)) {
            return;
        }
        if (is_numeric($opt)) {
            $where .= ' ' . $this->parseKey($this->opt['pri']) . "=$opt ";
        } else if (is_string($opt)) {
            $where .= " $opt ";
        } else if (is_array($opt)) {
            foreach ($opt as $key => $set) {
                if ($key[0] == '_') {
                    switch (strtolower($key)) {
                        case '_query':
                            parse_str($set, $q);
                            $this->where($q);
                            break;
                        case '_string':
                            $set = preg_match('@(AND|OR|XOR)\s*$@i', $set) ? $set : $set . ' AND ';
                            $where .= $set;
                            break;
                    }
                } else if (is_numeric($key)) { //参数为字符串
                    if (!preg_match('@(AND|OR|XOR)\s*$@i', $set)) {
                        $set .= isset($opt['_logic']) ? " {$opt['_logic']} " : ' AND ';
                    }
                    $where .= $set;
                } else if (is_string($key)) { //参数为数组
                    if (!is_array($set)) {
                        $logic = isset($opt['_logic']) ? " {$opt['_logic']} " : ' AND '; //连接方式
                        $where .= " ".( strpos($key,'.') === false ? $this->parseKey($key) : $key )." " . "='$set' " . $logic;
                    } else {
                        $logic = isset($opt['_logic']) ? " {$opt['_logic']} " : ' AND '; //连接方式
                        $logic = isset($set['_logic']) ? " {$set['_logic']} " : $logic; //连接方式
                        //连接方式
                        if (is_string(end($set)) && in_array(strtoupper(end($set)), array('AND', 'OR', 'XOR'))) {
                            $logic = ' ' . current($set) . ' ';
                        }
                        reset($set); //数组指针回位
                        //如: $map['username'] = array(array('gt', 3), array('lt', 5), 'AND');
                        if (is_array(current($set))) {
                            foreach ($set as $exp) {
                                if (is_array($exp)) {
                                    $t[$key] = $exp;
                                    $this->where($t);
                                    $this->opt['where'] .= strtoupper($logic);
                                }
                            }
                        } else {
                            $option = !is_array($set[1]) ? explode(',', $set[1]) : $set[1]; //参数
                            $key = strpos($key,'.') === false ? $this->parseKey($key) : $key;
                            switch (strtoupper($set[0])) {
                                case 'IN':
                                    $value = '';
                                    foreach ($option as $v) {
                                        $value .= is_numeric($v) ? $v . "," : "'" . $v . "',";
                                    }
                                    $value = trim($value, ',');
                                    $where .= " $key " . " IN ($value) $logic";
                                    break;
                                case 'NOTIN':
                                    $value = '';
                                    foreach ($option as $v) {
                                        $value .= is_numeric($v) ? $v . "," : "'" . $v . "',";
                                    }
                                    $value = trim($value, ',');
                                    $where .= " $key " . " NOT IN ($value) $logic";
                                    break;
                                case 'BETWEEN':
                                    $where .= " $key " . " BETWEEN " . $option[0] . ' AND ' . $option[1] . $logic;
                                    break;
                                case 'NOTBETWEEN':
                                    $where .= " $key " . " NOT BETWEEN " . $option[0] . ' AND ' . $option[1] . $logic;
                                    break;
                                case 'LIKE':
                                    foreach ($option as $v) {
                                        $where .= " $key " . " LIKE '$v' " . $logic;
                                    }
                                    break;
                                case 'NOTLIKE':
                                    foreach ($option as $v) {
                                        $where .= " $key " . " NOT LIKE '$v'" . $logic;
                                    }
                                    break;
                                case 'EQ':
                                    $where .= " $key " . '=' . (is_numeric($set[1]) ? $set[1] : "'{$set[1]}'") . $logic;
                                    break;
                                case 'NEQ':
                                    $where .= " $key " . '<>' . (is_numeric($set[1]) ? $set[1] : "'{$set[1]}'") . $logic;
                                    break;
                                case 'GT':
                                    $where .= " $key " . '>' . (is_numeric($set[1]) ? $set[1] : "'{$set[1]}'") . $logic;
                                    break;
                                case 'EGT':
                                    $where .= " $key " . '>=' . (is_numeric($set[1]) ? $set[1] : "'{$set[1]}'") . $logic;
                                    break;
                                case 'LT':
                                    $where .= " $key " . '<' . (is_numeric($set[1]) ? $set[1] : "'{$set[1]}'") . $logic;
                                    break;
                                case 'ELT':
                                    $where .= " $key " . '<=' . (is_numeric($set[1]) ? $set[1] : "'{$set[1]}'") . $logic;
                                    break;
                                case 'EXP':
                                    $where .= " $key " . $set[1] . $logic;
                                    break;
                            }
                        }
                    }
                }
            }
        }
        if (!empty($where)) {
            //删除尾部OR AND
            $where = preg_replace('@(OR|AND|XOR)\s*$@i', '', $where);
            if (empty($this->opt['where'])) {
                //第一次设置where
                $this->opt['where'] = "" . $where;
            } else if (preg_match('@(OR|AND|XOR)\s*$@i', $this->opt['where'])) {
                //有连接属性时使用连接属性
                $this->opt['where'] .= $where;
            } else {
                $this->opt['where'] .= ' AND ' . $where;
            }
        } else {
            $this->opt['where'] = preg_replace('@(OR|AND|XOR)\s*$@i', '', $this->opt['where']);
        }
    }

    /**
     * 查询字段处理
     * @param mixed $data
     * @param boolean $exclude 排除字段
     * @return mixed
     */
    public function field($data, $exclude = false)
    {
        //参数为空时,不进行操作
        if (empty($data)) {
            return;
        }
        if(true === $data) {// 获取全部字段
            $fields =  $this->fieldData;
            $data =  $fields?array_keys($fields):'*';
        }
        //字符串时转为数组
        if (!is_array($data)) {
            $data = explode(",", $data);
        }
        //排除字段
        if ($exclude) {
            $_data = $data;
            $fieldData = $this->opt['fieldData'];
            foreach ($_data as $name => $field) {
                if (isset($this->opt['fieldData'][$field])) {
                    unset($fieldData[$field]);
                }
            }
            $data = array_keys($fieldData);
        }
        $field = '';
        foreach ($data as $name => $d) {
            if (is_string($name)) {
                $field .= $name . ' AS ' . $d . ",";
            } else {
                $field .= $d . ',';
            }
        }
        $this->opt['field'] = substr($field, 0, -1);
    }

    /**
     * 删除表中所有数据
     * @param $table 数据表
     * @return mixed
     */
    public function delAll($table)
    {
        return $this->exec("DELETE FROM " . C('DB_PREFIX') . $table);
    }

    /**
     * 查询SQL组装 join
     * @param mixed $join
     * @param string $type JOIN类型
     * @return type
     */
    public function join($join, $type='INNER') {
        if(is_array($join)) {
            foreach ($join as $key=>&$_join){
                $_join = $this->replaceTable($_join);
                $_join = false !== stripos($_join,'JOIN')? $_join : strtoupper($type).' JOIN ' .$_join;
            }
            $this->opt['join'] = trim($join);
        } elseif(!empty($join)) {
            //将__table_name__字符串替换成带前缀的表名
            $join = $this->replaceTable($join);
            $join = false !== stripos($join,'JOIN')? $join : strtoupper($type).' JOIN '.$join;
            $this->opt['join'][] = trim($join);
        } elseif(is_null($join)) {
            $this->opt['join'] = array();
        }
    }

    /**
     * limit 操作
     * @param mixed $offset 起始位置
     * @param mixed $row 查询数量
     * @return type
     */
    public function limit($offset,$row=null)
    {
        if(is_null($row) && strpos($offset,',')){
            list($offset,$row) = explode(',',$offset);
        }
        $this->opt['limit'] = intval($offset).( $row? ','.intval($row) : '' );
    }

    /**
     * 指定分页
     * @access public
     * @param mixed $page 页数
     * @param mixed $rows 每页数量
     * @return type
     */
    public function page($page,$rows=null){
        if(is_null($rows) && strpos($page,',')){
            list($page, $rows) = explode(',',$page);
        }
        $this->opt['page'] = array(intval($page),intval($rows));
    }

    /**
     * 设置查询缓存时间
     * @param $time
     * @return number
     */
    public function cache($time = -1)
    {
        $this->opt['cacheTime'] = $time;
    }

    /**
     * 判断表中字段是否在存在
     * @param string $fieldName 字段名
     * @param string $table 表名(不带表前缀)
     * @return bool
     */
    public function fieldExists($fieldName, $table)
    {
        $field = $this->query("DESC " . C("DB_PREFIX") . $table);
        foreach ($field as $f) {
            if (strtolower($f['Field']) == strtolower($fieldName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 判断表是否存在
     * @param $tableName 表名
     * @return bool
     */
    public function tableExists($tableName)
    {
        $tableArr = $this->query("SHOW TABLES");
        foreach ($tableArr as $k => $table) {
            $tableTrue = $table['Tables_in_' . C('DB_DATABASE')];
            if (strtolower($tableTrue) == strtolower(C('DB_PREFIX') . $tableName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 字段值增加
     * 示例：$Db->inc("price","id=20",188)
     * 将id为20的记录的price字段值增加188
     * @param string $field 字段名
     * @param string $where 条件
     * @param int $step 增加数
     * @return mixed
     */
    public function inc($field)
    {
        //$whereStr = $where ? " WHERE " . $where : "";
        $args = func_get_args();
        $step = 1;
        if(count($args)==3) {
            $where = $args[1];
            $step = (int) $args[2];
            $this->where($where);
        }
        else if(count($args)==2) {
            if(is_numeric($args[1])) {
                $step = (int) $args[1];
            }
            else {
                $where = $args[1];
                $this->where($where);
            }
        }
        $whereStr = $this->parseWhere( $this->opt['where'] );
        $sql = "UPDATE " . $this->parseTable( $this->opt['table'] ) . " SET " . $this->parseKey($field) . '=' . $field . '+' . $step . $whereStr;
        return $this->exec($sql);
    }

    /**
     * 减少字段值
     * 示例：$Db->dec("price","id=20",20)
     * 将id为20的记录的price字段值增加20
     * @param string $field 字段名
     * @param string $where 条件
     * @param int $step 减少数
     * @return mixed
     */
    public function dec($field)
    {
        //$whereStr = $where ? " WHERE " . $where : "";
        $args = func_get_args();
        $step = 1;
        if(count($args)==3) {
            $where = $args[1];
            $step = (int) $args[2];
            $this->where($where);
        }
        else if(count($args)==2) {
            if(is_numeric($args[1])) {
                $step = (int) $args[1];
            }
            else {
                $where = $args[1];
                $this->where($where);
            }
        }
        $whereStr = $this->parseWhere( $this->opt['where'] );
        $sql = "UPDATE " . $this->parseTable( $this->opt['table'] ) . " SET " . $this->parseKey($field) . '=' . $field . '-' . $step . $whereStr;
        return $this->exec($sql);
    }

    /**
     * 创建数据库
     * @param $database 数据库名
     * @param string $charset 字符集
     * @return mixed
     */
    public function createDatabase($database, $charset = "utf8")
    {
        return $this->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARSET " . $charset);
    }

    /**
     * 删除表
     * @param string $table 表名
     * @return bool
     */
    public function dropTable($table)
    {
        return $this->exec("DROP TABLE IF EXISTS `" . C('DB_PREFIX') . $table . "`");
    }

    /**
     * 修复数据表
     * @param $table
     * @return bool
     */
    public function repair($table)
    {
        return $this->exec("REPAIR TABLE `" . C('DB_PREFIX') . $table . "`");
    }

    /**
     * 修改表名
     * @param $old 旧表名
     * @param $new 新表名
     */
    public function rename($old, $new)
    {
        return $this->exec("ALTER TABLE `" . C('DB_PREFIX') . $old . "` RENAME " . C('DB_PREFIX') . $new);
    }

    /**
     * 优化表解决表碎片问题
     * @param array $table 表
     * @return bool
     */
    public function optimize($table)
    {
        $this->exec("OPTIMIZE TABLE `" . C('DB_PREFIX') . $table . "`");
    }

    /**
     * 清空表数据
     * @param $table
     * @return mixed
     */
    public function truncate($table)
    {
        return $this->exec("TRUNCATE TABLE `" . C('DB_PREFIX') . $table . "`");
    }

    /**
     * 判断表名是否存在
     * @param $table 表名
     * @return bool
     */
    public function isTable($table)
    {
        //添加表前缀
        $table = C('DB_PREFIX') . $table;
        $info = $this->query('show tables');
        foreach ($info as $n => $d) {
            if ($table == current($d)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取主键名称
     * @access public
     * @return string
     */
    public function getPk() {
        return $this->pri ? $this->pri : ($this->opt['pri'] ? $this->opt['pri'] : null);
    }

    /**
     * 获得最后一条SQL
     * @return type
     */
    public function getLastSql()
    {
        return $this->querySql;
    }

    /**
     * 获得所有SQL语句
     * @return type
     */
    public function getAllSql()
    {
        return \Took\Debug::$sqlExeArr;
    }

    /**
     * 将查询SQL压入调试数组
     * @param void
     */
    protected function recordSql($sql)
    {
        $this->querySql = $sql;

    }

    /**
     * 数据库调试 记录当前SQL
     * @access protected
     * @param boolean $start  调试开始标记 true 开始 false 结束
     */
    protected function debug($start) {
        if(in_array('SQL',C('LOG_LEVEL'))) {
            if($start) {
                G('queryStartTime');
            }else{
                // 记录操作结束时间
                G('queryEndTime');
                $log = array(
                    'sql'=>$this->querySql,
                    'runtime'=>G('queryStartTime','queryEndTime'),
                );
                if (!preg_match('/\s*show /', $this->querySql)) {
                    \Took\Debug::$sqlExeArr[] = $log;
                }
                trace($log['sql'].' [ RunTime:'.$log['runtime'].'s ]', 'SQL', true);
            }
        }
    }

    //错误处理
    protected function error($error)
    {
        $this->error = $error;
        if (DEBUG) {
            halt($this->error);
        } else {
            log_write($this->error);
        }
    }

    /**
     * 获得所有表信息
     * @return  array
     */
    public function getAllTableInfo()
    {
        $info = $this->query("SHOW TABLE STATUS FROM " . C("DB_DATABASE"));
        $arr = array();
        $arr['total_size'] = 0; //总大小
        $arr['total_row'] = 0; //总条数
        foreach ($info as $k => $t) {
            $arr['table'][$t['Name']]['tablename'] = $t['Name'];
            $arr['table'][$t['Name']]['engine'] = $t['Engine'];
            $arr['table'][$t['Name']]['rows'] = $t['Rows'];
            $arr['table'][$t['Name']]['collation'] = $t['Collation'];
            $charset = $arr['table'][$t['Name']]['collation'] = $t['Collation'];
            $charset = explode("_", $charset);
            $arr['table'][$t['Name']]['charset'] = $charset[0];
            $arr['table'][$t['Name']]['dataFree'] = $t['Data_free'];//碎片大小
            $arr['table'][$t['Name']]['indexSize'] = $t['Index_length'];//索引大小
            $arr['table'][$t['Name']]['dataSize'] = $t['Data_length'];//数据大小
            $arr['table'][$t['Name']]['totalSize'] = $t['Data_free'] + $t['Data_length'] + $t['Index_length'];
            $fieldData = $this->getAllField($t['Name'],false);
            $arr['table'][$t['Name']]['field'] = $fieldData;
            $arr['table'][$t['Name']]['primaryKey'] = $this->getPrimaryKey($t['Name'],true);
            $arr['table'][$t['Name']]['autoincrement'] = $t['Auto_increment'] ? $t['Auto_increment'] : '';
            $arr['total_size'] += $arr['table'][$t['Name']]['dataSize'];
            $arr['total_row'] += $t['Rows'];
        }
        return $arr;
    }

    /**
     * 获得数据库大小
     * @return int
     */
    public function getDataBaseSize()
    {
        $sql = "show table status from " . C("DB_DATABASE");
        $data = $this->query($sql);
        $size = 0;
        foreach ($data as $v) {
            $size += $v['Data_length'] + $v['Data_length'] + $v['Index_length'];;
        }
        return $size;
    }

    /**
     * 获得数据表大小
     * @param $table 表名
     * @return mixed
     */
    public function getTableSize($table)
    {
        $table = C('DB_PREFIX') . $table;
        $sql = "show table status from " . C("DB_DATABASE");
        $data = $this->query($sql);
        foreach ($data as $v) {
            if ($v['Name'] == $table) {
                return $v['Data_length'] + $v['Index_length'];
            }
        }
        return 0;
    }

    /**
     * 生成查询SQL
     * @access public
     * @param array $options 表达式
     * @return string
     */
    public function buildSelectSql($options=array())
    {
        //有查询条件时
        $this->where($options);
        if(isset($this->opt['page'])) {
            // 根据页数计算limit
            list($page,$rows) = $this->opt['page'];
            $page = $page>0 ? $page : 1;
            $rows = $rows>0 ? $rows : (is_numeric($this->opt['limit'])?$this->opt['limit']:20);
            $offset = $rows*($page-1);
            $this->opt['limit'] =  $offset.','.$rows;
            unset($this->opt['page']);
        }
        $sql = $this->parseSql($this->selectSql, $this->opt);
        return $sql;
    }

    /**
     * 生成查询SQL 可用于子查询
     * @access public
     * @return string
     */
    public function buildSql() {
        $sql = $this->buildSelectSql();
        //初始化opt参数
        $this->optInit();
        return  '( '.$sql.' )';
    }

    /**
     * 替换SQL语句中表达式
     * @access public
     * @param string $sql
     * @param array $options 表达式
     * @return string
     */
    public function parseSql($sql,$options=array())
    {
        $sql = str_replace(
            array('%TABLE%','%DISTINCT%','%FIELD%','%JOIN%','%WHERE%','%GROUP%','%HAVING%','%ORDER%','%LIMIT%','%UNION%','%LOCK%','%COMMENT%','%FORCE%'),
            array(
                $this->parseTable($options['table']),
                $this->parseDistinct(isset($options['distinct'])?$options['distinct']:false),
                $this->parseField(!empty($options['field'])?$options['field']:'*'),
                $this->parseJoin(!empty($options['join'])?$options['join']:''),
                $this->parseWhere(!empty($options['where'])?$options['where']:''),
                $this->parseGroup(!empty($options['group'])?$options['group']:''),
                $this->parseHaving(!empty($options['having'])?$options['having']:''),
                $this->parseOrder(!empty($options['order'])?$options['order']:''),
                $this->parseLimit(!empty($options['limit'])?$options['limit']:''),
                $this->parseUnion(!empty($options['union'])?$options['union']:''),
                $this->parseLock(isset($options['lock'])?$options['lock']:false),
                $this->parseComment(!empty($options['comment'])?$options['comment']:''),
                $this->parseForce(!empty($options['force'])?$options['force']:'')
            ),$sql);
        return $sql;
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
     * table分析
     * @access protected
     * @param mixed $tables
     * @return string
     */
    protected function parseTable($tables) {
        if(is_array($tables)) { //支持别名定义
            $array = array();
            foreach ($tables as $table=>$alias){
                if(!is_numeric($table)) {
                    $array[] =  $this->parseKey($table).' '.$this->parseKey($alias);
                }
                else {
                    $array[] =  empty($this->opt['alias']) ? $this->parseKey($alias) : $this->parseKey($alias).' '.$this->parseKey($this->opt['alias']);
                }
            }
            $tables = implode(',', $array);
        } else if(is_string($tables)){
            $tables  =  explode(',',$tables);
            if(count($tables) > 1) {
                array_walk($tables, array(&$this, 'parseKey'));
            } else {
                // 数据表别名
                $tables = preg_replace('@\s+@', ' ', $tables[0]);
                list($tables, $alias) = array_pad(explode(' ',$tables),2,'');
                if(empty($alias)) {
                    $alias = isset($this->opt['alias']) ? $this->opt['alias'] : '';
                }
                if($alias) {
                    $tables = $this->parseKey($tables).' '.$this->parseKey($alias);
                } else {
                    $tables = $this->parseKey($tables);
                }
            }
            if(is_array($tables)) {
                $tables = implode(',', $tables);
            }
        }
        return $tables;
    }

    /**
     * distinct分析
     * @access protected
     * @param mixed $distinct
     * @return string
     */
    protected function parseDistinct($distinct) {
        return !empty($distinct) ? ' DISTINCT ' : '';
    }

    /**
     * field分析
     * @access protected
     * @param mixed $fields
     * @return string
     */
    protected function parseField($fields) {
        if(is_string($fields) && '' !== $fields) {
            $fields = explode(',',$fields);
        }
        if(is_array($fields)) {
            $array = array();
            foreach ($fields as $key=>$field){
                if(!is_numeric($key)) {
                    $array[] = $this->parseKey($key).' AS '. $this->parseKey($field);
                } else {
                    $array[] = $this->parseKey($field);
                }
            }
            $fieldsStr = implode(',', $array);
        }else{
            $fieldsStr = '*';
        }
        return $fieldsStr;
    }

    /**
     * where分析
     * @access protected
     * @param mixed $where
     * @return string
     */
    protected function parseWhere($where) {
        return  !empty($where)? ' WHERE '.$where:'';
    }

    /**
     * group分析
     * @access protected
     * @param mixed $group
     * @return string
     */
    protected function parseGroup($group) {
        return !empty($group)? ' GROUP BY '.$group:'';
    }

    /**
     * having分析
     * @access protected
     * @param string $having
     * @return string
     */
    protected function parseHaving($having) {
        return  !empty($having)? ' HAVING '.$having:'';
    }

    /**
     * order分析
     * @access protected
     * @param mixed $order
     * @return string
     */
    protected function parseOrder($order) {
        if(is_array($order)) {
            $array   =  array();
            foreach ($order as $key=>$val){
                if(is_numeric($key)) {
                    $array[] = $this->parseKey($val);
                }else{
                    $array[] = $this->parseKey($key).' '.$val;
                }
            }
            $order   =  implode(',',$array);
        }
        return !empty($order)?  ' ORDER BY '.$order:'';
    }

    /**
     * limit分析
     * @access protected
     * @param mixed $limit
     * @return string
     */
    protected function parseLimit($limit) {
        return !empty($limit)?   ' LIMIT '.$limit.' ':'';
    }

    /**
     * join分析
     * @access protected
     * @param mixed $join
     * @return string
     */
    protected function parseJoin($join) {
        $joinStr = '';
        if(!empty($join)) {
            $joinStr = ' '.implode(' ',$join).' ';
        }
        return $joinStr;
    }

    /**
     * union分析
     * @access protected
     * @param mixed $union
     * @return string
     */
    protected function parseUnion($union) {
        if(empty($union)) return '';
        if(isset($union['_all'])) {
            $str  =   'UNION ALL ';
            unset($union['_all']);
        }else{
            $str  =   'UNION ';
        }
        foreach ($union as $u){
            $sql[] = $str.(is_array($u)?$this->buildSelectSql($u):$u);
        }
        return implode(' ',$sql);
    }

    /**
     * comment分析
     * @access protected
     * @param string $comment
     * @return string
     */
    protected function parseComment($comment) {
        return  !empty($comment)?   ' /* '.$comment.' */':'';
    }

    /**
     * 设置锁机制
     * @access protected
     * @return string
     */
    protected function parseLock($lock=false) {
        return $lock?   ' FOR UPDATE '  :   '';
    }

    /**
     * index分析，可在操作链中指定需要强制使用的索引
     * @access protected
     * @param mixed $index
     * @return string
     */
    protected function parseForce($index) {
        if(empty($index)) return '';
        if(is_array($index)) $index = join(",", $index);
        return sprintf(" FORCE INDEX ( %s ) ", $index);
    }

    //将__table_name__字符串替换成带前缀的表名
    protected function replaceTable($table) {
        $prefix = C('DB_PREFIX');
        $table = preg_replace_callback("/__(\w+)__/sU", function ($match) use ($prefix) {
            $result = $prefix . strtolower($match[1]);
            return $this->parseKey($result);
        }, $table);
        return $table;
    }

    /**
     * 获取真实表名
     * @param string $table
     * @param string $prefix 表前缀
     * @return string
     * @access private
     */
    private function _getTable($table, $prefix = '')
    {
        if(preg_match('@__(\w+)__@', $table)) {
            $table = preg_replace('@__(\w+)__@', C('DB_PREFIX') . '\1', $table);
        } else {
            $table = (false === $prefix) ? $table : ($prefix ? $prefix. $table : $this->tablePrefix . $table);
        }
        return $table;
    }

}