<?php

/**
 * 数据库类文件
 * @abstract 用于数据库操作的支持
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Database
{
    private static $DBH = null; //存放PDO对象
    public function __construct($db_type, $db_host, $db_user, $db_pass, $db_name, $db_charset =
        'utf8', $db_pconnect = true)
    {
        if (!class_exists('PDO')) {
            die('您的服务器环境不支持PDO，请联系主机提供商解决');
        }
        if (!self::$DBH) {
            $dbh = new PDO("{$db_type}:host={$db_host};dbname={$db_name}", $db_user, $db_pass,
                array(PDO::ATTR_PERSISTENT => $db_pconnect));
            if (!$dbh) {
                die('无法连接数据库');
            }
            $dbh->query("SET NAMES `{$db_charset}`");
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$DBH = $dbh;
        }
        return true;
    }

    public function query($sql)
    {
        return self::$DBH->query($sql);
    }

    public function exec($sql)
    {
        return self::$DBH->exec($sql);
    }

    /**
     * @abstract 返回单条数据（一维数组）
     */
    public function getOneRecord($sql, $mode = PDO::FETCH_BOTH)
    {
        return $this->query($sql)->fetch($mode);
    }

    /**
     * @abstract 返回多条数据（多维数组）
     */
    public function getAllRecord($sql, $mode = PDO::FETCH_BOTH)
    {
        return $this->query($sql)->fetchAll($mode);
    }

    /**
     * @abstract 插入一条数据，返回数据插入后的ID值
     */
    public function insert($table, $key_val = array())
    {
        $colum = array();
        $value = array();
        if (!empty($key_val)) {
            foreach ($key_val as $key => $val) {
                $colum[] = "{$key}";
                $value[] = "'{$val}'";
            }
        }
        $sql = "insert into {$table}(" . implode(',', $colum) . ") values(" . implode(',',
            $value) . ")";
        return $this->exec($sql);
    }

    /**
     * @abstract 修改指定数据
     */
    public function update($table, $key_val = array(), $condition = array())
    {
        $key_val_tmp = array();
        foreach ($key_val as $key => $val) {
            if (!empty($val)) {
                $key_val_tmp[] = "`{$key}`='{$val}'";
            }
        }
        if (!empty($condition)) {
            $condition_tmp = array();
            foreach ($condition as $k => $v) {
                $condition_tmp[] = "{$k}='{$v}'";
            }
            $condition_tmp = ' where ' . implode(',', $condition_tmp);
        } else {
            $condition_tmp = '';
        }
        $sql = "update {$table} set " . implode(',', $key_val_tmp) . $condition_tmp;
        //dump($sql);
        return $this->exec($sql);
    }

    /**
     * @abstract 删除指定数据
     * @param array $key_val 目标数据信息 如"id"=>'18'，将删除ID=18的记录
     */
    public function delete($table, $key_val = array())
    {
        $key_val_tmp = array();
        foreach ($key_val as $key => $val) {
            if (!empty($val)) {
                $key_val_tmp[] = "`{$key}`='{$val}'";
            }
        }
        $sql = "delete from {$table} where " . implode(',', $key_val_tmp);
        return $this->exec($sql);
    }
}

?>