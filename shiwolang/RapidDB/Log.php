<?php
/**
 * Created by zhou.
 * User: zhou
 * Date: 2015/11/28
 * Time: 10:26
 */

namespace shiwolang\db;


class Log
{
    /**
     * @var string
     */
    protected $sql    = "";
    protected $params = [];
    /**
     * @var null|DB
     */
    protected $db = null;

    public function __construct($db, $sql = "", $params = [])
    {
        $this->db     = $db;
        $this->sql    = $sql;
        $this->params = $params;
    }

    public function getSql($raw = false)
    {
        if ($raw) {
            return $this->getRawSql();
        } else {
            return $this->sql;
        }
    }

    public function getRawSql()
    {
        if (empty($this->params)) {
            return $this->sql;
        }
        $params = $this->params;
        $sql    = '';
        if (isset($params[0])) {
            foreach (explode('?', $this->sql) as $i => $part) {
                if (!empty($part)) {
                    $param = (isset($params[$i]) ? $params[$i] : '');
                    $sql .= $part . $this->db->getPdo()->quote($param);
                }
            }
        } else {
            $sql = $this->sql;
            foreach ($params as $name => $param) {
                $sql = strtr($sql, [$name => $this->db->getPdo()->quote($param)]);
            }
        }

        return $sql;
    }


    function __debugInfo()
    {
        return [
            "sql"    => [
                "statement" => $this->getSql(),
                "params"    => $this->params
            ],
            "rawSql" => $this->getRawSql()
        ];
    }

    function __toString()
    {
        return $this->getRawSql();
    }
}