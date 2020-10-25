<?php

defined('IN_CART') or die;

/**
 *
 *
 * 数据库操作类
 *
 */
class DBMysql extends DB
{

    /**
     *
     * 	构造函数
     *
     */
    public function __construct($config = array())
    {
        extract($config);
        $this->conn = @mysql_connect($host . ":" . $port, $user, $pass, true);
        if (!$this->conn)
            halt(__("db_connect_error"));

        $this->dbprefix = $dbprefix;
        $this->querynum = 0;

        mysql_query("SET character_set_connection=$dbcharset, character_set_results=$dbcharset, character_set_client=binary", $this->conn);
        mysql_query("set sql_mode = ''", $this->conn);
        $this->dbcharset = $dbcharset;
        if (!mysql_select_db($dbname))
            halt(__("db_not_exist", $dbname));
    }

    public function __destruct()
    {
        if ($this->query && is_resource($this->query)) {
            mysql_free_result($this->query);
        }
        if ($this->conn) {
            mysql_close($this->conn);
        }
    }

    /**
     *
     * 	操作mysql
     *
     */
    public function query($sql)
    {
        $this->lastSql = $sql;
        $this->query = mysql_query($sql, $this->conn);

        $this->error = mysql_error();
        $this->errno = mysql_errno();

        if ($this->errno) {
            cerror("mysql error:" . $this->errno . "  " . $this->error);
        }
        $this->querynum++;
    }

    public function lastid()
    {
        return $this->conn ? mysql_insert_id($this->conn) : 0;
    }

    /**
     *
     * 获取结果集
     *
     */
    protected function getResults($keyid = '', $type = 'rows')
    {
        $return = array();
        if ($this->query) {
            if ($type == 'rows') {
                while ($row = mysql_fetch_assoc($this->query)) {
                    if (!$keyid) {
                        $return[] = $row;
                    } else {
                        $return[$row[$keyid]] = $row;
                    }
                }
            } else if ($type == 'row') {
                $return = mysql_fetch_assoc($this->query);
            }
        }
        return $return;
    }

    /**
     *
     * 过滤值
     *
     */
    public function escape_string($str)
    {
        return mysql_real_escape_string($str);
    }

    /**
     *
     * 查询影响的条数
     *
     */
    public function getAffectedRows()
    {
        return mysql_affected_rows($this->conn);
    }

}
