<?php

defined('IN_CART') or die;

/**
 *
 *
 * 数据库操作类
 *
 */
class DBSqlsrv extends DB
{

    protected $statement;

    /**
     *
     * 	构造函数
     *
     */
    public function __construct($config = array())
    {
        extract($config);
        $host .= $port ? "," . $port : '';
        $dsn = "sqlsrv:server=$host;database=$dbname";
        $this->conn = @new PDO($dsn, $user, $pass);
        if (!$this->conn)
            halt(__("db_connect_error"));

        $this->dbprefix = $dbprefix;
        $this->querynum = 0;
//		$this->conn->exec("SET character_set_connection=$dbcharset, character_set_results=$dbcharset, character_set_client=binary");
//		$this->conn->exec("set sql_mode = ''");
        $this->dbcharset = $dbcharset;
    }

    public function __destruct()
    {
        if ($this->statement) {
            $this->statement = null;
        }
        if ($this->conn) {
            $this->conn = null;
        }
    }

    /**
     *
     * 	操作mysql
     *
     */
    public function query($sql)
    {
        $sql = preg_replace("/`(.+?)`/", "\"\\1\"", $sql); //执行mysql的`替换成"

        $this->lastSql = $sql;
        $this->statement = $this->conn->prepare($sql);
        if (false === $this->statement) {
            
        }
        $result = $this->statement->execute();
        if (false === $result) {
            $error = $this->statement->errorInfo();
            $this->error = $error[2];
            $this->errno = $this->statement->errorCode();
            if ($this->errno) {
                cerror("sqlsrv error:" . $this->errno . "  " . $this->error);
            }
        }
        $this->querynum++;
    }

    public function lastid()
    {
        return $this->conn ? $this->conn->lastInsertId() : 0;
    }

    /**
     *
     * 查询操作，$keyid判断返回数组的key
     *
     */
    public function select($tableName, $fields, $where = "", $orderby = "", $limit = "", $keyid = "", $distinct = false)
    {
        if ($fields == "*") {
            $fieldstr = "*";
        } elseif ($distinct) { //唯一，只取一个元素，selectcol使用
            $fieldstr = "distinct " . $this->escapekey($fields);
        } else {
            $fieldstr = implode(",", array_map(array($this, "escapekey"), explode(",", $fields)));
        }
        $sql = "SELECT $fieldstr "
                . "FROM " . $this->getTableName($tableName)
                . $this->buildWhere($where)
                . $this->buildOrderBy($orderby)
                . $this->buildLimit($limit)
        ;
        $this->query($sql);
        return $this->getResults($keyid);
    }

    /**
     *
     * 过滤字段
     *
     */
    private function escapekey($key)
    {
        return '"' . $key . '"';
    }

    /**
     *
     * 获取结果集
     *
     */
    protected function getResults($keyid = '', $type = 'rows')
    {
        $return = array();
        if ($this->statement) {
            if ($type == "rows") {
                $rows = $this->statement->fetchAll(PDO::FETCH_ASSOC);
                if ($keyid) {
                    foreach ($rows as $key => $row) {
                        $return[$row[$keyid]] = $row;
                    }
                } else {
                    return $rows;
                }
            } else if ($type == 'row') {
                $return = $this->statement->fetch(PDO::FETCH_ASSOC);
            }
        }

        return $return;
    }

    /**
     *
     * 查询影响的条数
     *
     */
    public function getAffectedRows()
    {
        return $thie->statement ? $this->statement->rowCount() : 0;
    }

}
