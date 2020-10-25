<?php
/**
 * 模型类文件
 * @abstract 一表一模型，一表一对象，操作无非增删改查。
 * @author 暮雨秋晨
 * @copyright 2014
 */

require_once 'M/ModelException.php';

class Model
{
    private static $DBH = null; //PDO对象
    private static $tables = array(); //存储数据表
    private $table; //存储表名称

    private function __construct($table)
    {
        if (!self::$DBH) {
            throw new ModelException('Model of uninitialized, please use the static method <b>init</b> to initialize.',
                2);
        }
        $this->table = strtolower($table);
    }

    public static function getInstance($table)
    {
        if (isset(self::$tables[$table]) && is_object(self::$tables[$table])) {
            return self::$tables[$table];
        } else {
            $table_obj = new self($table);
            self::$tables[$table] = $table_obj;
            return $table_obj;
        }
    }

    public static function init($db_type, $db_host, $db_user, $db_pass, $db_name, $db_charset =
        'utf8', $db_pconnect = true)
    {
        if (!class_exists('PDO')) {
            throw new ModelException('Your server does not support the <b>PDO</b>.', 2);
        }
        if (!self::$DBH) {
            $dbh = new PDO("{$db_type}:host={$db_host};dbname={$db_name}", $db_user, $db_pass,
                array(PDO::ATTR_PERSISTENT => $db_pconnect));
            if (!$dbh) {
                throw new ModelException('Unable to establish connection with the database server.',
                    2);
            }
            $dbh->query("SET NAMES `{$db_charset}`");
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$DBH = $dbh;
        }
        if (empty(self::$tables)) {
            $tables = $dbh->query('show TABLES')->fetchAll(PDO::FETCH_NUM);
            foreach ($tables as $v) {
                self::$tables[$v[0]] = new Model($v[0]);
            }
        }
    }

    /**
     * 基础SQL执行方法
     */
    public function query($sql)
    {
        return self::$DBH->query($sql);
    }

    public function exec($sql)
    {
        return self::$DBH->exec($sql);
    }

    /**
     * 增
     */
    public function insert(array $data)
    {
        $sql = 'insert into ' . $this->table . '(';
        foreach ($data as $k => $v) {
            $sql .= "`{$k}`,";
        }
        $sql = rtrim($sql, ',') . ') values(';
        foreach ($data as $v) {
            $sql .= "'{$v}',";
        }
        $sql = rtrim($sql, ',') . ');';
        return $this->exec($sql);
    }

    /**
     * 删
     */
    public function delete($cond)
    {
        $sql = 'delete from ' . $this->table . ' where ';
        if (is_array($cond)) {
            foreach ($cond as $k => $v) {
                $cond[$k] = "`{$k}`='{$v}'";
            }
            $sql .= implode(' and ', $cond);
        } else {
            $sql .= $cond;
        }
        return $this->exec($sql);
    }

    /**
     * 改
     */
    public function update(array $data, $cond = null)
    {
        $sql = 'update ' . $this->table . ' set ';
        foreach ($data as $k => $v) {
            $data[$k] = "`{$k}`='{$v}'";
        }
        $sql .= implode(',', $data);
        if (!empty($cond)) {
            if (is_array($cond)) {
                foreach ($cond as $k => $v) {
                    $cond[$k] = "`{$k}`='{$v}'";
                }
                $sql .= ' where ' . implode(' and ', $cond);
            } else {
                $sql .= ' where ' . $cond;
            }
        }
        return $this->exec($sql);
    }

    /**
     * 查(单条)
     */
    public function fetch($data = null, $cond = null, $mode = PDO::FETCH_ASSOC)
    {
        return $this->query($this->BuildFetch($data, $cond))->fetch($mode);
    }

    /**
     * 查(多条)
     */
    public function fetchAll($data = null, $cond = null, $mode = PDO::FETCH_ASSOC)
    {
        return $this->query($this->BuildFetch($data, $cond))->fetchAll($mode);
    }

    private function BuildFetch($data, $cond)
    {
        if ($data == null || empty($data)) {
            $data = '*';
        } elseif (is_array($data)) {
            $data = implode(',', $data);
        } else {
            $data = $data;
        }
        $sql = 'select ' . $data . ' from ' . $this->table;
        if (!empty($cond)) {
            if (is_array($cond)) {
                foreach ($cond as $k => $v) {
                    $cond[$k] = "`{$k}`='{$v}'";
                }
                $sql .= ' where ' . implode(' and ', $cond);
            } else {
                $sql .= ' ' . $cond;
            }
        }
        return $sql;
    }

    /**
     * 获取插入的ID
     */
    public function getInsertID()
    {
        return self::$DBH->lastInsertId();
    }
}
?>