<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM\Query;

use \Cute\ORM\Database;
use \Cute\ORM\HandlerSocket;


/**
 * 查询
 */
class Builder
{
    const COUNT_INSERT_BULK_MAX = 500; //批量插入一次最多条数

    protected $db = null;
    protected $table = '';
    protected $nothing = false; //不查询，直接返回空
    protected $constrains = [];
    protected $or_constrains = [];
    protected $offset = 0;
    protected $length = -1;
    protected $additions = [
        'GROUP BY' => null, 'HAVING' => null,
        'ORDER BY' => null,
    ];

    public function __construct(Database& $db, $table)
    {
        $this->db = $db;
        $this->table = $table;
    }
    
    public function __toString()
    {
        list($sql, $params) = $this->getSelectSQL();
        return $this->getDB()->embed($sql, $params);
    }

    public function getDB()
    {
        return $this->db;
    }

    public function setNothing($nothing = true)
    {
        $this->nothing = $nothing;
        return $this;
    }

    public function orderBy($order, $orient = '')
    {
        if ($orient) {
            $order .= ' ' . strtoupper($orient);
        }
        $this->additions['ORDER BY'] = $order;
        return $this;
    }

    public function groupBy($group, $having = null)
    {
        $this->additions['GROUP BY'] = $group;
        $this->additions['HAVING'] = $having;
        return $this;
    }

    /**
     * 分页
     */
    public function setPage($page_size, $page_no = 1, $total = 0)
    {
        $this->length = intval($page_size) < 0 ? -1 : intval($page_size);
        if ($this->length > 0) {
            $page_no = intval($page_no); //0表示不分页，负数是反向页码
            if ($page_no < 0 && $total > 0) {
                $last_page = ceil($total / $this->length);
                $page_no += $last_page + 1; //反向页码，同时能检查页码是否越界
            }
            if ($page_no > 0) {
                $this->offset = ($page_no - 1) * $this->length;
            }
        }
        return $this;
    }

    protected function parseTail($exclude = '')
    {
        $excludes = func_get_args();
        $additions = ''; //分组、排序
        foreach ($this->additions as $key => $value) {
            if (!is_null($value) && !in_array($key, $excludes)) {
                $additions .= ' ' . $key . ' ' . $value;
            }
        }
        return $additions;
    }

    protected function parseWhere()
    {
        if ($this->nothing) {
            return ['WHERE 1=0', []];
        }
        $where = '';
        $params = [];
        if ($this->constrains) {
            $where = implode(' AND ', array_keys($this->constrains));
            $params = exec_function_array('array_merge', array_values($this->constrains));
        }
        if ($this->or_constrains) {
            $or_where = '(' . implode(') OR (', array_keys($this->or_constrains)) . ')';
            $or_params = exec_function_array('array_merge', array_values($this->or_constrains));
            $where = empty($where) ? $or_where : '(' . $where . ') OR (' . $or_where . ')';
            $params = empty($params) ? $or_params : array_merge($params, $or_params);
        }
        $where = empty($where) ? '' : 'WHERE ' . $where;
        return [$where, $params];
    }

    public function orElse($cond, array $values = [])
    {
        assert(substr_count($cond, '?') === count($values));
        $this->or_constrains[$cond] = $values;
        return $this;
    }

    public function find($cond, $values = [])
    {
        if (is_array($values)) {
            $values = array_values($values);
        } else {
            $values = array_slice(func_get_args(), 1);
        }
        assert(substr_count($cond, '?') === count($values));
        $this->constrains[$cond] = $values;
        return $this;
    }

    public function findBy($field, $values = [], $not = false)
    {
        if (! is_array($values)) {
            $values = is_null($values) ? [] : [$values];
        }
        $count = count($values);
        if ($count === 0) {
            $op = ($not ? ' IS NOT ' : ' IS ') . 'NULL';
        } else if ($count === 1) {
            $op = ($not ? ' <> ' : ' = ') . '?';
        } else {
            $marks = implode(', ', array_fill(0, $count, '?'));
            $op = ($not ? ' NOT IN ' : ' IN ') . '(' . $marks . ')';
        }
        return $this->find($field . $op, $values);
    }

    public static function getInsertSQL($table_name, array $columns,
                                        $delay = false, $replace = false)
    {
        if ($replace === true) {
            $verb = $delay ? 'REPLACE DELAYED' : 'REPLACE INTO';
        } else {
            $verb = $delay ? 'INSERT DELAYED' : 'INSERT INTO';
        }
        if ($count = count($columns)) {
            $columns = implode(',', $columns);
            $marks = implode(', ', array_fill(0, $count, '?'));
            return ["$verb $table_name ($columns)", $marks];
        } else {
            return ["$verb $table_name", ''];
        }
    }

    /**
     * 往表中插入一行
     * @param array $newbie 插入的字段和值，关联数组
     * @param boolean $delay 延迟写入
     * @return 自增ID
     */
    public function insert(array $newbie, $replace = false)
    {
        $db = $this->getDB();
        $table_name = $db->getTableName($this->table, true);
        list($sql, $marks) = self::getInsertSQL($table_name,
            array_keys($newbie), false, $replace);
        if (!empty($marks)) {
            $sql .= " VALUES ($marks)";
            $params = array_values($newbie);
            if ($db->execute($sql, $params)) {
                return $db->getPDO()->lastInsertId();
            }
        }
    }

    /**
     * 插于多行
     */
    public function insertBulk(array $rows, array $columns = null, $delay = false)
    {
        assert(count($rows) > 0);
        if (empty($columns)) {
            $columns = array_keys(reset($rows));
        }
        $db = $this->getDB();
        $table_name = $db->getTableName($this->table, true);
        list($sql, $marks) = self::getInsertSQL($table_name, $columns, $delay, false);
        $chunks = array_chunk($rows, self::COUNT_INSERT_BULK_MAX);
        foreach ($chunks as $chunk) {
            $more_marks = array_fill(0, count($chunk), "($marks)");
            $sql .= " VALUES " . implode(', ', $more_marks);
            $more_values = array_map('array_values', $chunk);
            $params = exec_funcution_array('array_merge', $more_values);
            $db->execute($sql, $params);
        }
    }

    /**
     * 删除或清空
     */
    public function delete()
    {
        $db = $this->getDB();
        $table_name = $db->getTableName($this->table, true);
        list($where, $params) = $this->parseWhere();
        $sql = "DELETE FROM $table_name $where";
        if (empty($where) && $db->getDriverName() === 'mysql') {
            $sql = "TRUNCATE $table_name";
        }
        return $db->execute(rtrim($sql), $params);
    }

    /**
     * 获取更新的SET部分
     */
    public static function getUpdateSet(array $changes)
    {
        $sets = [];
        $values = [];
        foreach ($changes as $key => $val) {
            $sets[] = $key . '=?';
            $values[] = $val;
        }
        $setsql = "SET " . implode(', ', $sets);
        return [$setsql, $values];
    }

    /**
     * 更新表中的一些字段
     * @param array $changes 更新的字段和值，关联数组
     * @param boolean $delay 延迟写入
     * @param string $cond 条件
     * @param array $args 条件中替代值
     * @return 影响的行数
     */
    public function update(array $changes, $delay = false)
    {
        $db = $this->getDB();
        list($where, $params) = $this->parseWhere();
        list($setsql, $values) = self::getUpdateSet($changes);
        $verb = $delay ? 'UPDATE DELAYED' : 'UPDATE';
        $table_name = $db->getTableName($this->table, true);
        $sql = "$verb $table_name $setsql $where";
        $params = array_merge($values, $params);
        return $db->execute(rtrim($sql), $params);
    }

    public static function getColumnString($columns = '*')
    {
        if (is_string($columns)) {
            return $columns;
        }
        $col_array = (array)$columns;
        $columns = [];
        foreach ($col_array as $alias => $column) {
            if (is_string($alias)) {
                $columns[] = $column . ' AS ' . $alias;
            } else {
                $columns[] = $column;
            }
        }
        return implode(', ', $columns);
    }

    /**
     * 获取要查询的字段形式
     */
    public static function getSelectFrom($table_name, $columns = '*', $prefix = "")
    {
        if (is_object($columns)) {
            $columns = get_object_vars($columns);
        }
        if (is_array($columns)) { //字段使用as别名
            array_walk($columns, create_function('&$v,$k',
                'if(!is_numeric($k))$v.=" as ".$k;'));
            $columns = implode(', ', $columns);
        }
        $columns = trim($columns);
        $sql = "SELECT $prefix$columns FROM $table_name";
        return $sql;
    }
    
    public function getSelectSQL($columns = '*')
    {
        $top = "";
        $limit = "";
        $columns = self::getColumnString($columns);
        $db = $this->getDB();
        if (starts_with($columns, 'COUNT')) {
            $additions = $this->parseTail('ORDER BY');
        } else {
            $additions = $this->parseTail();
            if ($this->length > 0) {
                list($top, $limit) = $db->getLimit($this->length, $this->offset);
            }
        }
        $table_name = $db->getTableName($this->table, true);
        list($where, $params) = $this->parseWhere();
        $sql = self::getSelectFrom($table_name, $columns, $top);
        $sql .= ($where ? ' ' . $where : '') . $additions . $limit;
        return array($sql, $params);
    }

    /**
     * Select查询
     */
    public function select($columns = '*')
    {
        list($sql, $params) = $this->getSelectSQL($columns);
        return $this->getDB()->query(rtrim($sql), $params);
    }

    public function apply($func)
    {
        $func = strtoupper($func);
        $args = array_slice(func_get_args(), 2);
        if (empty($args)) {
            $columns = $func === 'COUNT' ? '*' : '';
        } else {
            $columns = implode(', ', $args);
        }
        $columns = $func . '(' . $columns . ')';
        return $this->select($columns);
    }

    public function hsBind(HandlerSocket& $hs, array $fields = [])
    {
        $db = $this->getDB();
        $dbname = $db->getDBName();
        $table_name = $db->getTableName($this->getTable(), false);
        $hs->open($dbname, $table_name, $fields);
        return $hs;
    }
}
