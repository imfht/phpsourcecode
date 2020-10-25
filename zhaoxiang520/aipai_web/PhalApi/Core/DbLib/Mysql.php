<?php

/**
 * Mysql.php
 * @since   2016-08-31
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 * @link http://medoo.in/
 */
namespace PhalApi\Core\DbLib;

use PhalApi\Core\Exception\PAException;

class Mysql extends BaseDbLib {

    protected $handle;
    protected $prefix;
    protected $dbType;
    protected $dbDebug;

    public function __construct( $options ) {
        $this->dbType = $options['DB_TYPE'];
        $this->prefix = $options['DB_PREFIX'];
        $this->dbDebug = $options['DB_DEBUG'];
        try {
            $params = [
                \PDO::ATTR_CASE              => \PDO::CASE_NATURAL,
                \PDO::ATTR_ERRMODE           => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_ORACLE_NULLS      => \PDO::NULL_NATURAL,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
                \PDO::ATTR_EMULATE_PREPARES  => false,
            ];
            if( !empty($options['DB_PARAMS']) ){
                $params = array_merge($params, $options['DB_PARAMS']);
            }
            if ( isset($options['DB_PORT']) && is_int($options['DB_PORT'] * 1) ) {
                $port = $options['DB_PORT'];
            }
            $dsn = 'mysql:host=' . $options['DB_HOST'] . (isset($port) ? ';port=' . $port : '') . ';dbname=' . $options['DB_NAME'];
            $this->handle = new \PDO($dsn, $options['DB_USER'], $options['DB_PWD'], $params);
            $this->handle->exec( 'SET SQL_MODE=ANSI_QUOTES' );
            if ( $options['DB_CHARSET'] ) {
                $this->handle->exec( "SET NAMES '" . $options['DB_CHARSET'] . "'" );
            }
        } catch (\PDOException $e) {
            throw new PAException($e->getMessage());
        }
    }

    /**
     * 查询实现
     * @param $table
     * @param $join
     * @param null $columns
     * @param null $where
     * @return array|bool
     */
    public function select($table, $join, $columns = null, $where = null) {
        $column = $where == null ? $join : $columns;
        $is_single_column = (is_string($column) && $column !== '*');
        $query = $this->query($this->selectContext($table, $join, $columns, $where));
        $stack = [];$index = 0;
        if ( !$query ) {
            return false;
        }
        if ($columns === '*') {
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        }
        if ($is_single_column) {
            return $query->fetchAll(\PDO::FETCH_COLUMN);
        }
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            foreach ($columns as $key => $value) {
                if (is_array($value)) {
                    $this->dataMap($index, $key, $value, $row, $stack);
                } else {
                    $this->dataMap($index, $key, preg_replace('/^[\w]*\./i', "", $value), $row, $stack);
                }
            }
            $index++;
        }
        return $stack;
    }

    /**
     * 数据插入
     * @param $table
     * @param $dataArr
     * @return array|mixed
     */
    public function insert($table, $dataArr) {
        $lastId = [];
        if (!isset($dataArr[ 0 ])) {
            $dataArr = [$dataArr];
        }
        foreach ($dataArr as $data) {
            $values = [];$columns = [];
            foreach ($data as $key => $value) {
                $columns[] = $this->columnQuote(preg_replace('/^(\(JSON\)\s*|#)/i', "", $key));
                switch (gettype($value)) {
                    case 'NULL':
                        $values[] = 'NULL';
                        break;
                    case 'array':
                        preg_match('/\(JSON\)\s*([\w]+)/i', $key, $columnMatch);
                        $values[] = isset($columnMatch[ 0 ]) ?
                            $this->quote(json_encode($value)) :
                            $this->quote(serialize($value));
                        break;
                    case 'boolean':
                        $values[] = ($value ? '1' : '0');
                        break;
                    case 'integer':
                    case 'double':
                    case 'string':
                        $values[] = $this->fnQuote($key, $value);
                        break;
                }
            }
            $this->exec('INSERT INTO ' . $this->tableQuote($table) . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ')');
            $lastId[] = $this->handle->lastInsertId();
        }
        return count($lastId) > 1 ? $lastId : $lastId[ 0 ];
    }

    /**
     * 数据更新
     * @param $table
     * @param $data
     * @param null $where
     * @return bool|int
     */
    public function update($table, $data, $where = null) {
        $fields = [];
        foreach ($data as $key => $value) {
            preg_match('/([\w]+)(\[(\+|\-|\*|\/)\])?/i', $key, $match);
            if (isset($match[ 3 ])) {
                if (is_numeric($value)) {
                    $fields[] = $this->columnQuote($match[ 1 ]) . ' = ' . $this->columnQuote($match[ 1 ]) . ' ' . $match[ 3 ] . ' ' . $value;
                }
            } else {
                $column = $this->columnQuote(preg_replace('/^(\(JSON\)\s*|#)/i', "", $key));
                switch (gettype($value)) {
                    case 'NULL':
                        $fields[] = $column . ' = NULL';
                        break;
                    case 'array':
                        preg_match('/\(JSON\)\s*([\w]+)/i', $key, $column_match);

                        $fields[] = $column . ' = ' . $this->quote(
                                isset($column_match[ 0 ]) ? json_encode($value) : serialize($value)
                            );
                        break;
                    case 'boolean':
                        $fields[] = $column . ' = ' . ($value ? '1' : '0');
                        break;
                    case 'integer':
                    case 'double':
                    case 'string':
                        $fields[] = $column . ' = ' . $this->fnQuote($key, $value);
                        break;
                }
            }
        }
        return $this->exec('UPDATE ' . $this->tableQuote($table) . ' SET ' . implode(', ', $fields) . $this->whereClause($where));
    }

    /**
     * 数据删除
     * @param $table
     * @param $where
     * @return bool|int
     */
    public function delete($table, $where) {
        return $this->exec('DELETE FROM ' . $this->tableQuote($table) . $this->whereClause($where));
    }

    /**
     * 用新的数据替换老数据
     * @param $table
     * @param $columns
     * @param null $search
     * @param null $replace
     * @param null $where
     * @return bool|int
     */
    public function replace($table, $columns, $search = null, $replace = null, $where = null) {
        if (is_array($columns)) {
            $replaceQuery = array();
            foreach ($columns as $column => $replacements) {
                foreach ($replacements as $replaceSearch => $replaceReplacement) {
                    $replaceQuery[] = $column . ' = REPLACE(' . $this->columnQuote($column) . ', ' . $this->quote($replaceSearch) . ', ' . $this->quote($replaceReplacement) . ')';
                }
            }
            $replaceQuery = implode(', ', $replaceQuery);
            $where = $search;
        } else {
            if (is_array($search)) {
                $replaceQuery = array();
                foreach ($search as $replaceSearch => $replaceReplacement) {
                    $replaceQuery[] = $columns . ' = REPLACE(' . $this->columnQuote($columns) . ', ' . $this->quote($replaceSearch) . ', ' . $this->quote($replaceReplacement) . ')';
                }
                $replaceQuery = implode(', ', $replaceQuery);
                $where = $replace;
            } else {
                $replaceQuery = $columns . ' = REPLACE(' . $this->columnQuote($columns) . ', ' . $this->quote($search) . ', ' . $this->quote($replace) . ')';
            }
        }
        return $this->exec('UPDATE ' . $this->tableQuote($table) . ' SET ' . $replaceQuery . $this->whereClause($where));
    }

    /**
     * 查询一条符合条件的记录
     * @param $table
     * @param null $join
     * @param null $columns
     * @param null $where
     * @return bool|mixed
     */
    public function get($table, $join = null, $columns = null, $where = null) {
        $column = $where == null ? $join : $columns;
        $isSingleColumn = (is_string($column) && $column !== '*');
        $query = $this->query($this->selectContext($table, $join, $columns, $where) . ' LIMIT 1');
        if ($query) {
            $data = $query->fetchAll(\PDO::FETCH_ASSOC);
            if (isset($data[ 0 ])) {
                if ($isSingleColumn) {
                    return $data[ 0 ][ preg_replace('/^[\w]*\./i', "", $column) ];
                }
                if ($column === '*') {
                    return $data[ 0 ];
                }
                $stack = [];
                foreach ($columns as $key => $value) {
                    if (is_array($value)) {
                        $this->dataMap(0, $key, $value, $data[ 0 ], $stack);
                    } else {
                        $this->dataMap(0, $key, preg_replace('/^[\w]*\./i', "", $value), $data[ 0 ], $stack);
                    }
                }
                return $stack[ 0 ];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 判断目标数据是否存在
     * @param $table
     * @param $join
     * @param null $where
     * @return bool
     */
    public function has($table, $join, $where = null) {
        $column = null;
        $query = $this->query('SELECT EXISTS(' . $this->selectContext($table, $join, $column, $where, 1) . ')');
        if ($query) {
            return $query->fetchColumn() == '1';
        } else {
            return false;
        }
    }

    /**
     * 统计符合条件数据的个数
     * @param $table
     * @param null $join
     * @param null $column
     * @param null $where
     * @return bool|int
     */
    public function count($table, $join = null, $column = null, $where = null) {
        $query = $this->query($this->selectContext($table, $join, $column, $where, 'COUNT'));
        return $query ? 0 + $query->fetchColumn() : false;
    }

    /**
     * 求最大值
     * @param $table
     * @param $join
     * @param null $column
     * @param null $where
     * @return bool|int|string
     */
    public function max($table, $join, $column = null, $where = null) {
        $query = $this->query($this->selectContext($table, $join, $column, $where, 'MAX'));
        if ($query) {
            $max = $query->fetchColumn();
            return is_numeric($max) ? $max + 0 : $max;
        } else {
            return false;
        }
    }

    /**
     * 求最小值
     * @param $table
     * @param $join
     * @param null $column
     * @param null $where
     * @return bool|int|string
     */
    public function min($table, $join, $column = null, $where = null) {
        $query = $this->query($this->selectContext($table, $join, $column, $where, 'MIN'));
        if ($query) {
            $min = $query->fetchColumn();
            return is_numeric($min) ? $min + 0 : $min;
        } else {
            return false;
        }
    }

    /**
     * 求均值
     * @param $table
     * @param $join
     * @param null $column
     * @param null $where
     * @return bool|int
     */
    public function avg($table, $join, $column = null, $where = null) {
        $query = $this->query($this->selectContext($table, $join, $column, $where, 'AVG'));
        return $query ? 0 + $query->fetchColumn() : false;
    }

    /**
     * 求和
     * @param $table
     * @param $join
     * @param null $column
     * @param null $where
     * @return bool|int
     */
    public function sum($table, $join, $column = null, $where = null) {
        $query = $this->query($this->selectContext($table, $join, $column, $where, 'SUM'));
        return $query ? 0 + $query->fetchColumn() : false;
    }

    public function action($actions) {
        if (is_callable($actions)) {
            $this->handle->beginTransaction();
            $result = $actions($this);
            if ($result === false) {
                $this->handle->rollBack();
            } else {
                $this->handle->commit();
            }
        } else {
            return false;
        }
    }

    /**
     * 获取最后一个操作的错误信息
     * @return array
     */
    public function error() {
        return $this->handle->errorInfo();
    }

    /**
     * 获取数据库信息
     * @return array
     */
    public function info() {
        $output = [
            'server' => 'SERVER_INFO',
            'driver' => 'DRIVER_NAME',
            'client' => 'CLIENT_VERSION',
            'version' => 'SERVER_VERSION',
            'connection' => 'CONNECTION_STATUS'
        ];
        foreach ($output as $key => $value) {
            $output[ $key ] = $this->handle->getAttribute(constant('PDO::ATTR_' . $value));
        }
        return $output;
    }

}