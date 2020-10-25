<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------

/**
 * Mysql数据库基类
 * @package     Db
 * @subpackage  Driver
 * @author      后盾向军 <houdunwangxj@gmail.com>
 */
abstract class Db implements DbInterface
{

    public $table = NULL; //表名
    public $pri = null; //默认表主键
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
        'cacheTime' => null//查询缓存时间
    ); //SQL 操作
    public $lastSql; //最后发送的SQL
    public $error = NULL; //错误信息

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
     * 数据库连接
     * 根据配置文件获得数据库连接对象
     * @param string $table
     * @param bool $full 全表名
     * @return Object   连接对象
     */
    public function link($table, $full)
    {
        //通过数据驱动如MYSQLI连接数据库
        if ($this->connect()) {
            if ($table) {
                /**
                 * 初始化表
                 */
                $this->table($table, $full);
                /**
                 * 数据表
                 */
                $this->table = $this->opt['table'];
                /**
                 * 表字段数据
                 */
                $this->fieldData = $this->opt['fieldData'];
                /**
                 * 表主键
                 */
                $this->pri = $this->opt['pri'];

            }
            return $this->link;
        } else {
            $this->error('数据库连接错误');
        }
    }

    /**
     * 初始化表字段与主键
     * @param $table
     * @param $full
     * @return bool
     */
    public function table($table, $full = false)
    {
        /**
         * 初始化opt参数
         */
        $this->optInit();

        /**
         * 字段集
         */
        $fieldData = $this->getAllField($table, $full);

        /**
         * 表主键
         */
        $pri = $this->getPrimaryKey($table, $full);
        /**
         * 设置选项
         */
        $this->opt['table'] = $full ? $table : C('DB_PREFIX') . $table;
        $this->opt['fieldData'] = $fieldData;
        $this->opt['pri'] = $pri;
    }

    /**
     * 获得表结构及主键
     * 查询表结构获得所有字段信息，用于字段缓存
     * @access private
     * @param string $table
     * @param bool $full
     * @return array
     */
    public function getAllField($table, $full = false)
    {
        /**
         * 不是全表名是添加表前缀
         */
        if (!$full) {
            $table = C('DB_PREFIX') . $table;
        }
        $name = C('DB_DATABASE') . '.' . $table;
        //字段缓存
        if (!DEBUG && F($name, false, APP_TABLE_PATH)) {
            $fieldData = F($name, false, APP_TABLE_PATH);
        } else {
            $sql = "show columns from `$table`";
            if (!$result = $this->query($sql)) {
                return false;
            }
            $fieldData = array();
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
            DEBUG || F($name, $fieldData, APP_TABLE_PATH);
        }
        return $fieldData;
    }

    /**
     * 获得表主键字段
     * @param string $table 数据表
     * @param bool $full
     * @return bool
     */
    private function getPrimaryKey($table, $full = false)
    {
        $fieldData = $this->getAllField($table, $full);
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
     * @return void
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
        /**
         * 设置查询字段
         */
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
     * @param string $where
     * @return array|string
     */
    public function select($where = '')
    {
        /**
         * 有查询条件时
         */
        $this->where($where);
        /**
         * 组合查询SQL
         */
        $sql = 'SELECT ' . $this->opt['field'] . ' FROM ' . $this->opt['table'] .
            $this->opt['where'] . $this->opt['group'] . $this->opt['having'] .
            $this->opt['order'] . $this->opt['limit'];
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
        /**
         * 更新数据不能为空
         */
        if (!$value) {
            $this->optInit();
            return false;
        }
        $sql = $type . " INTO " . $this->opt['table'] . "(" . implode(',', $value['fields']) . ")" .
            "VALUES (" . implode(',', $value['values']) . ")";
        return $this->exe($sql);
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
        /**
         * 更改必须有条件
         * 如果更新数据中有主键,则以主键做为条件使用
         */
        if (empty($this->opt['where'])) {
            if (isset($data[$this->opt['pri']])) {
                $this->opt['where'] = " WHERE " . $this->opt['pri'] . " = " . intval($data[$this->opt['pri']]);
            } else {
                return false;
            }
        }
        /**
         * 检测更新数据字段合法性
         * 与字段安全处理
         */
        $data = $this->formatField($data);
        /**
         * 没有更新数据
         */
        if (empty($data)) {
            $this->optInit();
            return false;
        }
        $sql = "UPDATE " . $this->opt['table'] . " SET ";
        foreach ($data['fields'] as $n => $field) {
            $sql .= $field . "=" . $data['values'][$n] . ',';
        }
        $sql = trim($sql, ',') . $this->opt['where'] . $this->opt['limit'];
        return $this->exe($sql);
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
        $sql = "DELETE FROM " . $this->opt['table'] . $this->opt['where'] . $this->opt['limit'];
        return $this->exe($sql);
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
        /**
         * 没有查询条件
         */
        if (empty($opt)) {
            return;
        }
        if (is_numeric($opt)) {
            $where .= ' ' . $this->opt['pri'] . "=$opt ";
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
                        $where .= " $key " . "='$set' " . $logic;
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
            /**
             * 删除尾部OR AND
             */
            $where = preg_replace('@(OR|AND|XOR)\s*$@i', '', $where);
            if (empty($this->opt['where'])) {
                /**
                 * 第一次设置where
                 */
                $this->opt['where'] = " WHERE " . $where;
            } else if (preg_match('@(OR|AND|XOR)\s*$@i', $this->opt['where'])) {
                /**
                 * 有连接属性时使用连接属性
                 */
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
        /**
         * 参数为空时,不进行操作
         */
        if (empty($data)) {
            return;
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
        return $this->exe("DELETE FROM " . C('DB_PREFIX') . $table);
    }


    //join多表关联
    public function join($join)
    {
        $join = preg_replace('@__(\w+)__@', C('DB_PREFIX') . '\1', $join);
        $this->opt['table'] = $join;
    }

    /**
     * limit 操作
     * @param mixed $data
     * @return type
     */
    public function limit($data)
    {
        $this->opt['limit'] = " LIMIT $data ";
    }

    /**
     * SQL 排序 ORDER
     * @param type $data
     */
    public function order($data)
    {
        $this->opt['order'] = " ORDER BY $data ";
    }

    /**
     * 分组操作
     * @param type $opt
     */
    public function group($opt)
    {
        $this->opt['group'] = " GROUP BY $opt";
    }

    /**
     * 分组条件having
     * @param type $opt
     */
    public function having($opt)
    {
        $this->opt['having'] = " HAVING $opt";
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
     * 统计
     */
    public function count($field = '*')
    {
        $sql = "SELECT count($field) AS c FROM " . $this->opt['table'] .$this->opt['where'] . $this->opt['group']
            . $this->opt['having'] .$this->opt['order'] . $this->opt['limit'];
        $data = $this->query($sql);
        return $data ? $data[0]['c'] : $data;
    }

    /**
     * 求最大值
     */
    public function max($field)
    {
        $sql = "SELECT max($field) AS c FROM " .$this->opt['table'] .$this->opt['where'] . $this->opt['group']
            . $this->opt['having'] .$this->opt['order'] . $this->opt['limit'];
        $data = $this->query($sql);
        return $data ? $data[0]['c'] : $data;
    }

    /**
     * 求最小值
     */
    public function min($field)
    {
        $sql = "SELECT min($field) AS c FROM " . $this->opt['table'] .$this->opt['where'] . $this->opt['group']
            . $this->opt['having'] .$this->opt['order'] . $this->opt['limit'];
        $data = $this->query($sql);
        return $data ? $data[0]['c'] : $data;
    }

    /**
     * 求平均值
     */
    public function avg($field)
    {
        $sql = "SELECT avg($field) AS c FROM " . $this->opt['table'] .$this->opt['where'] . $this->opt['group']
            . $this->opt['having'] .$this->opt['order'] . $this->opt['limit'];
        $data = $this->query($sql);
        return $data ? $data[0]['c'] : $data;
    }

    /**
     * SQL中的SUM计算
     */
    public function sum($field)
    {
        $sql = "SELECT sum($field) AS c FROM " . $this->opt['table'] .$this->opt['where'] . $this->opt['group']
            . $this->opt['having'] .$this->opt['order'] . $this->opt['limit'];
        $data = $this->query($sql);
        return $data ? $data[0]['c'] : $data;
    }

    /**
     * 字段值增加
     * 示例：$Db->dec("price","id=20",188)
     * 将id为20的记录的price字段值增加188
     * @param $field 字段名
     * @param $where 条件
     * @param int $step 增加数
     * @return mixed
     */
    public function inc($field, $where, $step = 1)
    {
        $sql = "UPDATE " . $this->opt['table'] . " SET " . $field . '=' . $field . '+' . $step . " WHERE " . $where;
        return $this->exe($sql);
    }

    /**
     * 减少字段值
     * @param $field
     * @param $where
     * @param int $step
     * @return mixed
     */
    public function dec($field, $where, $step = 1)
    {
        $sql = "UPDATE " . $this->opt['table'] . " SET " . $field . '=' . $field . '-' . $step . " WHERE " . $where;
        return $this->exe($sql);
    }

    /**
     * 创建数据库
     * @param $database 数据库名
     * @param string $charset 字符集
     * @return mixed
     */
    public function createDatabase($database, $charset = "utf8")
    {
        return $this->exe("CREATE DATABASE IF NOT EXISTS `$database` CHARSET " . $charset);
    }

    /**
     * 删除表
     * @param string $table 表名
     * @return bool
     */
    public function dropTable($table)
    {
        return $this->exe("DROP TABLE IF EXISTS `" . C('DB_PREFIX') . $table . "`");
    }

    /**
     * 修复数据表
     * @param $table
     * @return bool
     */
    public function repair($table)
    {
        return $this->exe("REPAIR TABLE `" . C('DB_PREFIX') . $table . "`");
    }

    /**
     * 修改表名
     * @param $old 旧表名
     * @param $new 新表名
     */
    public function rename($old, $new)
    {
        return $this->exe("ALTER TABLE `" . C('DB_PREFIX') . $old . "` RENAME " . C('DB_PREFIX') . $new);
    }

    /**
     * 优化表解决表碎片问题
     * @param array $table 表
     * @return bool
     */
    public function optimize($table)
    {
        $this->exe("OPTIMIZE TABLE `" . C('DB_PREFIX') . $table . "`");
    }

    /**
     * 清空表数据
     * @param $table
     * @return mixed
     */
    public function truncate($table)
    {
        return $this->exe("TRUNCATE TABLE `" . C('DB_PREFIX') . $table . "`");
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
     * 获得最后一条SQL
     * @return type
     */
    public function getLastSql()
    {
        return array_pop(Debug::$sqlExeArr);
    }

    /**
     * 获得所有SQL语句
     * @return type
     */
    public function getAllSql()
    {
        return Debug::$sqlExeArr;
    }

    /**
     * 将查询SQL压入调试数组
     * @param void
     */
    protected function recordSql($sql)
    {
        if (!preg_match('/\s*show /', $sql)) {
            Debug::$sqlExeArr[] = $sql;
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
            $fieldData = $this->getAllField($t['Name'],true);
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
}