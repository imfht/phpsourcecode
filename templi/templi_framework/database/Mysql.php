<?php
/**
 * 已废弃
 * mysql数据库操作 驱动类
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date 2013-1-19
 */
namespace framework\database;
use framework\core\Abnormal;
require_once 'Db.php';

class Mysql extends DB
{
    /** @var resource  mysql 连接资源 */
    private $linkid = NULL;     //连接id
    /** @var  resource 最后一次查询的资源 */
    private $lastqueryid;

    /**
     * 连接数据库
     */
    public function connect()
    {
        if (isset($this->pconnect)) {
            $this->linkid = @mysql_pconnect($this->dbhost, $this->dbuser, $this->dbpsw, 1);
        } else {
            $this->linkid = @mysql_connect($this->dbhost, $this->dbuser, $this->dbpsw, 1);
        }
        if (!$this->linkid && APP_DEBUG) {
            throw new Abnormal($this->errorMsg('Can not link to mysql'), 500);
        }
        if ($this->dbname) {
            mysql_select_db($this->dbname, $this->linkid) or $this->errorMsg('Can not use db ' . $this->dbname);
            $this->execute(' set names ' . $this->charset);
        } elseif (APP_DEBUG) {
            throw new Abnormal($this->errorMsg('It not gave dbname'), 500);
        }
    }

    /**
     * 执行基本的 mysql查询
     */
    function query($sql)
    {
        if (!$sql) {
            return false;
        } else {
            if (!$this->linkid && APP_DEBUG) {
                throw new Abnormal($this->errorMsg('未连接数据库'), 500);
            }
            $this->lastqueryid = mysql_query($sql, $this->linkid);
            if ($this->lastqueryid === false && APP_DEBUG) {
                throw new Abnormal($this->errorMsg($this->error(), $sql), 500);
            } else {
                return $this->fetchArray($this->lastqueryid);
            }
        }
    }

    /**
     * 执行mysql 语句
     */
    public function execute($sql)
    {
        if (!$sql) {
            return false;
        }
        if (!$this->linkid && APP_DEBUG) {
            throw new Abnormal($this->errorMsg('未连接数据库'), 500);
        }
        $this->lastqueryid = mysql_query($sql, $this->linkid);
        if ($this->lastqueryid === false && APP_DEBUG) {
            throw new Abnormal($this->errorMsg($this->error(), $sql), 500);
        } else {
            return $this->affectedRows();
        }
    }

    /**
     * 将mysql查询结果转化为数组
     */
    function fetchArray($handler)
    {
        $list = array();
        while ($row = mysql_fetch_assoc($handler)) {
            $list[] = $row;
        }
        return $list;
    }

    /**
     * 获取最后一次添加数据的主键号
     */
    function insertId()
    {
        return mysql_insert_id($this->linkid);
    }

    /**
     * 释放查询资源
     */
    function freeResult()
    {
        mysql_free_result($this->lastqueryid);
        $this->lastqueryid = NULL;
        return true;
    }

    /**
     * 获取mysql产生的文本错误信息
     */
    function error()
    {
        return mysql_error($this->linkid);
    }

    /**
     * 返回mysql 操作中的文本错误编码
     */
    function erron()
    {
        return mysql_errno($this->linkid);
    }

    /**
     * 返回最后一次操作数据库影响的条数
     */
    function affectedRows()
    {
        return mysql_affected_rows($this->linkid);
    }

    /**
     * 关闭数据库连接
     * @return bool
     */
    public function close()
    {
        $res = false;
        if (is_resource($this->linkid)) {
            $res = mysql_close($this->linkid);
        }
        $this->linkid = null;
        return $res;
    }

    public function __destruct()
    {
        $this->freeResult();
        $this->close();
    }
}
