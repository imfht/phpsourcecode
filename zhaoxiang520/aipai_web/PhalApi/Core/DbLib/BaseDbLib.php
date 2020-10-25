<?php
/**
 * BaseDbLib.php
 * @since   2016-09-02
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace PhalApi\Core\DbLib;

use PhalApi\Core\Log;
use PhalApi\Core\Response;

class BaseDbLib {

    /**
     * @var \PDO
     */
    protected $handle;
    protected $prefix;
    protected $dbType;
    protected $dbDebug;
    protected $logs = [];

    /**
     * 获取数据库链接句柄
     * @return mixed
     */
    public function getInstance(){
        return $this->handle;
    }

    /**
     * 获取最后一条查询语句
     * @return mixed
     */
    public function lastQuery() {
        return end($this->logs);
    }

    /**
     * 获取全部SQL语句
     * @return array
     */
    public function log() {
        return $this->logs;
    }

    /**
     * 准备sql语句
     * @param $query
     * @return bool|\PDOStatement
     */
    public function query($query){
        $this->logs[] = $query;
        $start = microtime(true);
        $res = $this->handle->query($query);
        if ($this->dbDebug) {
            $end = microtime(true);
            Response::debug(['SQL' => $query]);
            $cost = number_format($start - $end, 6);
            (new Log())->recordSQL($query, $cost);
            $this->dbDebug = false;
        }
        return $res;
    }

    /**
     * 执行sql语句
     * @param $query
     * @return int
     */
    public function exec($query) {
        $this->logs[] = $query;
        $start = microtime(true);
        $res = $this->handle->exec($query);
        if ($this->dbDebug) {
            $end = microtime(true);
            Response::debug(['SQL' => $query]);
            $cost = number_format($start - $end, 6);
            (new Log())->recordSQL($query, $cost);
            $this->dbDebug = false;
        }
        return $res;
    }

    public function quote( $string ) {
        return $this->handle->quote($string);
    }

    /**
     * 格式化数据表名（加上前缀）
     * @param $table
     * @return string
     */
    protected function tableQuote( $table ) {
        return '"' . $this->prefix . $table . '"';
    }

    protected function columnQuote( $string ) {
        preg_match('/(\(JSON\)\s*|^#)?([a-zA-Z0-9_]*)\.([a-zA-Z0-9_]*)/', $string, $columnMatch);
        if (isset($columnMatch[ 2 ], $columnMatch[ 3 ])) {
            return '"' . $this->prefix . $columnMatch[ 2 ] . '"."' . $columnMatch[ 3 ] . '"';
        }
        return '"' . $string . '"';
    }

    protected function columnPush( &$columns ){
        if ($columns == '*') {
            return $columns;
        }
        if (is_string($columns)) {
            $columns = [$columns];
        }
        $stack = [];
        foreach ($columns as $key => $value) {
            if (is_array($value)) {
                $stack[] = $this->columnPush($value);
            } else {
                preg_match('/([a-zA-Z0-9_\-\.]*)\s*\(([a-zA-Z0-9_\-]*)\)/i', $value, $match);
                if (isset($match[ 1 ], $match[ 2 ])) {
                    $stack[] = $this->columnQuote( $match[ 1 ] ) . ' AS ' . $this->columnQuote( $match[ 2 ] );
                    $columns[ $key ] = $match[ 2 ];
                } else {
                    $stack[] = $this->columnQuote( $value );
                }
            }
        }
        return implode($stack, ',');
    }

    protected function innerConjunct( $data, $conjunctor, $outerConjunctor ){
        $haystack = array();
        foreach ($data as $value) {
            $haystack[] = '(' . $this->dataImplode($value, $conjunctor) . ')';
        }
        return implode($outerConjunctor . ' ', $haystack);
    }

    protected function arrayQuote( $array ){
        $temp = array();
        foreach ($array as $value) {
            $temp[] = is_int($value) ? $value : $this->quote($value);
        }
        return implode($temp, ',');
    }

    protected function fnQuote( $column, $string ){
        return (strpos($column, '#') === 0 && preg_match('/^[A-Z0-9\_]*\([^)]*\)$/', $string)) ? $string : $this->quote($string);
    }

    protected function dataImplode( $data, $conjunct ) {
        $wheres = [];
        foreach ($data as $key => $value) {
            $type = gettype($value);
            if (preg_match('/^(AND|OR)(\s+#.*)?$/i', $key, $relation_match) && $type == 'array') {
                $wheres[] = 0 !== count(array_diff_key($value, array_keys(array_keys($value)))) ?
                    '(' . $this->dataImplode($value, ' ' . $relation_match[ 1 ]) . ')' :
                    '(' . $this->innerConjunct($value, ' ' . $relation_match[ 1 ], $conjunct) . ')';
            } else {
                preg_match('/(#?)([\w\.\-]+)(\[(\>|\>\=|\<|\<\=|\!|\<\>|\>\<|\!?~)\])?/i', $key, $match);
                $column = $this->columnQuote($match[ 2 ]);
                if (isset($match[ 4 ])) {
                    $operator = $match[ 4 ];
                    if ($operator == '!') {
                        switch ($type) {
                            case 'NULL':
                                $wheres[] = $column . ' IS NOT NULL';
                                break;
                            case 'array':
                                $wheres[] = $column . ' NOT IN (' . $this->arrayQuote($value) . ')';
                                break;
                            case 'integer':
                            case 'double':
                                $wheres[] = $column . ' != ' . $value;
                                break;
                            case 'boolean':
                                $wheres[] = $column . ' != ' . ($value ? '1' : '0');
                                break;
                            case 'string':
                                $wheres[] = $column . ' != ' . $this->fnQuote($key, $value);
                                break;
                        }
                    }
                    if ($operator == '<>' || $operator == '><') {
                        if ($type == 'array') {
                            if ($operator == '><') {
                                $column .= ' NOT';
                            }
                            if (is_numeric($value[ 0 ]) && is_numeric($value[ 1 ])) {
                                $wheres[] = '(' . $column . ' BETWEEN ' . $value[ 0 ] . ' AND ' . $value[ 1 ] . ')';
                            } else {
                                $wheres[] = '(' . $column . ' BETWEEN ' . $this->quote($value[ 0 ]) . ' AND ' . $this->quote($value[ 1 ]) . ')';
                            }
                        }
                    }
                    if ($operator == '~' || $operator == '!~') {
                        if ($type != 'array') {
                            $value = array($value);
                        }
                        $like_clauses = array();
                        foreach ($value as $item) {
                            $item = strval($item);
                            if (preg_match('/^(?!(%|\[|_])).+(?<!(%|\]|_))$/', $item)) {
                                $item = '%' . $item . '%';
                            }
                            $like_clauses[] = $column . ($operator === '!~' ? ' NOT' : '') . ' LIKE ' . $this->fnQuote($key, $item);
                        }
                        $wheres[] = implode(' OR ', $like_clauses);
                    }
                    if (in_array($operator, array('>', '>=', '<', '<='))) {
                        if (is_numeric($value)) {
                            $wheres[] = $column . ' ' . $operator . ' ' . $value;
                        } elseif (strpos($key, '#') === 0) {
                            $wheres[] = $column . ' ' . $operator . ' ' . $this->fnQuote($key, $value);
                        } else {
                            $wheres[] = $column . ' ' . $operator . ' ' . $this->quote($value);
                        }
                    }
                } else {
                    switch ($type) {
                        case 'NULL':
                            $wheres[] = $column . ' IS NULL';
                            break;
                        case 'array':
                            $wheres[] = $column . ' IN (' . $this->arrayQuote($value) . ')';
                            break;
                        case 'integer':
                        case 'double':
                            $wheres[] = $column . ' = ' . $value;
                            break;
                        case 'boolean':
                            $wheres[] = $column . ' = ' . ($value ? '1' : '0');
                            break;
                        case 'string':
                            $wheres[] = $column . ' = ' . $this->fnQuote($key, $value);
                            break;
                    }
                }
            }
        }
        return implode($conjunct . ' ', $wheres);
    }

    protected function whereClause( $where ){
        $whereClause = '';
        if (is_array($where)) {
            $whereKeys = array_keys($where);
            $whereAND = preg_grep('/^AND\s*#?$/i', $whereKeys);
            $whereOR = preg_grep('/^OR\s*#?$/i', $whereKeys);
            $singleCondition = array_diff_key($where, array_flip(
                ['AND', 'OR', 'GROUP', 'ORDER', 'HAVING', 'LIMIT', 'LIKE', 'MATCH']
            ));
            if ($singleCondition != []) {
                $condition = $this->dataImplode($singleCondition, '');
                if ($condition != '') {
                    $whereClause = ' WHERE ' . $condition;
                }
            }
            if (!empty($whereAND)) {
                $value = array_values($whereAND);
                $whereClause = ' WHERE ' . $this->dataImplode($where[ $value[ 0 ] ], ' AND');
            }
            if (!empty($whereOR)) {
                $value = array_values($whereOR);
                $whereClause = ' WHERE ' . $this->dataImplode($where[ $value[ 0 ] ], ' OR');
            }
            if (isset($where[ 'MATCH' ])) {
                $MATCH = $where[ 'MATCH' ];
                if (is_array($MATCH) && isset($MATCH[ 'columns' ], $MATCH[ 'keyword' ])) {
                    $whereClause .= ($whereClause != '' ? ' AND ' : ' WHERE ') . ' MATCH ("' . str_replace('.', '"."', implode($MATCH[ 'columns' ], '", "')) . '") AGAINST (' . $this->quote($MATCH[ 'keyword' ]) . ')';
                }
            }
            if (isset($where[ 'GROUP' ])) {
                $whereClause .= ' GROUP BY ' . $this->columnQuote($where[ 'GROUP' ]);
                if (isset($where[ 'HAVING' ])) {
                    $whereClause .= ' HAVING ' . $this->dataImplode($where[ 'HAVING' ], ' AND');
                }
            }
            if (isset($where[ 'ORDER' ])) {
                $ORDER = $where[ 'ORDER' ];
                if (is_array($ORDER)) {
                    $stack = array();
                    foreach ($ORDER as $column => $value) {
                        if (is_array($value)) {
                            $stack[] = 'FIELD(' . $this->columnQuote($column) . ', ' . $this->arrayQuote($value) . ')';
                        } else if ($value === 'ASC' || $value === 'DESC') {
                            $stack[] = $this->columnQuote($column) . ' ' . $value;
                        } else if (is_int($column)) {
                            $stack[] = $this->columnQuote($value);
                        }
                    }
                    $whereClause .= ' ORDER BY ' . implode($stack, ',');
                } else {
                    $whereClause .= ' ORDER BY ' . $this->columnQuote($ORDER);
                }
            }
            if (isset($where[ 'LIMIT' ])) {
                $LIMIT = $where[ 'LIMIT' ];
                if (is_numeric($LIMIT)) {
                    $whereClause .= ' LIMIT ' . $LIMIT;
                }
                if (is_array($LIMIT) && is_numeric($LIMIT[ 0 ]) && is_numeric($LIMIT[ 1 ])) {
                    if ($this->dbType === 'pgsql') {
                        $whereClause .= ' OFFSET ' . $LIMIT[ 0 ] . ' LIMIT ' . $LIMIT[ 1 ];
                    } else {
                        $whereClause .= ' LIMIT ' . $LIMIT[ 0 ] . ',' . $LIMIT[ 1 ];
                    }
                }
            }
        } else {
            if ( !is_null($where) ) {
                $whereClause .= ' ' . $where;
            }
        }
        return $whereClause;
    }

    protected function selectContext( $table, $join, &$columns = null, $where = null, $columnFn = null ) {
        preg_match('/([a-zA-Z0-9_\-]*)\s*\(([a-zA-Z0-9_\-]*)\)/i', $table, $tableMatch);
        if (isset($tableMatch[ 1 ], $tableMatch[ 2 ])) {
            $table = $this->tableQuote($tableMatch[ 1 ]);
            $tableQuery = $this->tableQuote($tableMatch[ 1 ]) . ' AS ' . $this->tableQuote($tableMatch[ 2 ]);
        } else {
            $table = $this->tableQuote($table);
            $tableQuery = $table;
        }
        $joinKey = is_array($join) ? array_keys($join) : null;
        if (isset($joinKey[ 0 ]) && strpos($joinKey[ 0 ], '[') === 0) {
            $tableJoin = [];
            $joinArray = [
                '>' => 'LEFT',
                '<' => 'RIGHT',
                '<>' => 'FULL',
                '><' => 'INNER'
            ];

            foreach($join as $subTable => $relation) {
                preg_match('/(\[(\<|\>|\>\<|\<\>)\])?([a-zA-Z0-9_\-]*)\s?(\(([a-zA-Z0-9_\-]*)\))?/', $subTable, $match);
                if ($match[ 2 ] != '' && $match[ 3 ] != '') {
                    if (is_string($relation)) {
                        $relation = 'USING ("' . $relation . '")';
                    }
                    if (is_array($relation)) {
                        if (isset($relation[ 0 ])) {
                            $relation = 'USING ("' . implode($relation, '", "') . '")';
                        } else {
                            $joins = [];
                            foreach ($relation as $key => $value) {
                                $joins[] = (strpos($key, '.') > 0 ? $this->columnQuote($key) : $table . '."' . $key . '"') .
                                    ' = ' . $this->tableQuote(isset($match[ 5 ]) ? $match[ 5 ] : $match[ 3 ]) . '."' . $value . '"';
                            }
                            $relation = 'ON ' . implode($joins, ' AND ');
                        }
                    }
                    $tableName = $this->tableQuote($match[ 3 ]) . ' ';
                    if (isset($match[ 5 ])) {
                        $tableName .= 'AS ' . $this->tableQuote($match[ 5 ]) . ' ';
                    }
                    $tableJoin[] = $joinArray[ $match[ 2 ] ] . ' JOIN ' . $tableName . $relation;
                }
            }
            $tableQuery .= ' ' . implode($tableJoin, ' ');
        } else {
            if (is_null($columns)) {
                if (is_null($where)) {
                    if (is_array($join) && isset($columnFn)) {
                        $where = $join;
                        $columns = null;
                    } else {
                        $where = null;
                        $columns = $join;
                    }
                } else {
                    $where = $join;
                    $columns = null;
                }
            } else {
                $where = $columns;
                $columns = $join;
            }
        }
        if (isset($columnFn)) {
            if ($columnFn == 1) {
                $column = '1';
                if (is_null($where)) {
                    $where = $columns;
                }
            } else {
                if (empty($columns)) {
                    $columns = '*';
                    $where = $join;
                }
                $column = $columnFn . '(' . $this->columnPush($columns) . ')';
            }
        } else {
            $column = $this->columnPush($columns);
        }

        return 'SELECT ' . $column . ' FROM ' . $tableQuery . $this->whereClause($where);
    }

    protected function dataMap($index, $key, $value, $data, &$stack ){
        if (is_array($value)) {
            $subStack = [];
            foreach ($value as $subKey => $subValue) {
                if (is_array($subValue)) {
                    $currentStack = $stack[ $index ][ $key ];
                    $this->dataMap(false, $subKey, $subValue, $data, $currentStack);
                    $stack[ $index ][ $key ][ $subKey ] = $currentStack[ 0 ][ $subKey ];
                } else {
                    $this->dataMap(false, preg_replace('/^[\w]*\./i', "", $subValue), $subKey, $data, $subStack);
                    $stack[ $index ][ $key ] = $subStack;
                }
            }
        } else {
            if ($index !== false) {
                $stack[ $index ][ $value ] = $data[ $value ];
            } else {
                if (preg_match('/[a-zA-Z0-9_\-\.]*\s*\(([a-zA-Z0-9_\-]*)\)/i', $key, $keyMatch)) {
                    $key = $keyMatch[ 1 ];
                }
                $stack[ $key ] = $data[ $key ];
            }
        }
    }

}