<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM;

use \DateTime;
use \PDO;
use \PDOException;
use \Cute\ORM\Literal;
use \Cute\Utility\Inflect;
use \Cute\View\Templater;


/**
 * 数据库
 */
abstract class Database
{
    const DB_ACTION_READ = 'R';
    const DB_ACTION_WRITE = 'W';

    protected static $past_sqls = [];
    protected $manager = null;
    protected $pdo = null;
    protected $dbname = '';
    protected $tblpre = '';

    public function __construct(\PDO $pdo, $dbname = '', $tblpre = '')
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($dbname) {
            $this->exchange($dbname);
        }
        $this->tblpre = $tblpre;
    }

    public function exchange($dbname, $create = false)
    {
        $this->dbname = ''; //如果下面操作失败，将通过查询确定当前数据库
        $this->doExchange($dbname, $create);
        if ($dbname && !array_key_exists($dbname, self::$past_sqls)) {
            self::$past_sqls[$dbname] = [];
        }
        $this->dbname = $dbname;
        return $this;
    }

    public function getPastSQL($dbname = false, $offset = 0)
    {
        if ($dbname === true || $dbname === '*') {
            return self::$past_sqls;
        } else if (empty($dbname)) {
            $dbname = $this->getDBName();
        }
        $sqls = & self::$past_sqls[$dbname];
        if (empty($offset)) {
            return $sqls;
        } else {
            return array_slice($sqls, -abs($offset), null, true);
        }
    }

    public function getManager()
    {
        if (!$this->manager) {
            $class = __NAMESPACE__ . '\\Manager';
            $this->manager = new $class($this);
        }
        return $this->manager;
    }

    public function getPrefix()
    {
        return $this->tblpre;
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    public function getDriverName()
    {
        $driver = $this->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME);
        $driver = strtolower($driver);
        return $driver === 'dblib' ? 'sqlsrv' : $driver;
    }

    public function quote($param)
    {
        if ($param instanceof Literal) {
            return strval($param);
        } else if ($param instanceof DateTime) {
            return "'" . $param->format('Y-m-d H:i:s') . "'";
        } else {
            $type = is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR;
            return $this->getPDO()->quote($param, $type);
        }
    }

    public function embed($sql, array $params = [])
    {
        foreach ($params as & $param) {
            $param = $this->quote($param);
        }
        $sql = str_replace('?', '%s', $sql);
        return vsprintf($sql, $params);
    }

    public function execute($sql, array $params = [])
    {
        if (!empty($params)) {
            $sql = $this->embed($sql, $params);
        }
        try {
            $result = $this->getPDO()->exec($sql);
        } catch (PDOException $e) {
            $message = "SQL: $sql\n" . $e->getMessage();
            throw new PDOException($message);
        }
        if (SQL_VERBOSE) {
            self::$past_sqls[$this->getDBName()][] = [
                'act' => self::DB_ACTION_WRITE, 'sql' => $sql,
            ];
        }
        return $result;
    }

    public function query($sql, array $params = [])
    {
        try {
            $stmt = $this->getPDO()->prepare($sql);
            if ($stmt->execute($params)) {
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $sql = $this->embed($sql, $params);
            $message = "SQL: $sql\n" . $e->getMessage();
            throw new PDOException($message);
        }
        if (SQL_VERBOSE) {
            $sql = $this->embed($sql, $params);
            self::$past_sqls[$this->getDBName()][] = [
                'act' => self::DB_ACTION_READ, 'sql' => $sql,
            ];
        }
        return $stmt; //false是查询失败
    }

    public function queryCol($sql, array $params = [], $column = 0)
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetchColumn($column);
        $stmt->closeCursor();
        return $result;
    }

    public function queryPairs($sql, array $params = [])
    {
        $result = [];
        $stmt = $this->query($sql, $params);
        $stmt->bindColumn(1, $value);
        $stmt->bindColumn(2, $key);
        while ($stmt->fetch(PDO::FETCH_BOUND)) {
            if ($key === '' || is_null($key)) {
                $result[] = $value;
            } else {
                $result[$key] = $value;
            }
        }
        $stmt->closeCursor();
        return $result;
    }

    public function transact(callable $transaction)
    {
        $pdo = $this->getPDO();
        if ($pdo->beginTransaction()) {
            $args = func_get_args();
            array_unshift($args, $this);
            try {
                $transaction($args);
                $pdo->commit();
            } catch (PDOException $e) {
                $pdo->rollBack();
            }
        }
    }

    public function queryTable($table)
    {
        $class = __NAMESPACE__ . '\\Query\\Builder';
        return new $class($this, $table);
    }

    public function queryModel($model = false)
    {
        $class = __NAMESPACE__ . '\\Query\\ResultSet';
        if (empty($model)) {
            $model = __NAMESPACE__ . '\\Model';
        }
        $name_args = array_slice(func_get_args(), 1);
        return new $class($this, $model, $name_args);
    }

    abstract public function doExchange($dbname, $create = false);

    abstract public function getDBName();

    abstract public function getTableName($table, $quote = false);

    abstract public function getLimit($length, $offset = 0);
}
