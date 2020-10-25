<?php

/**
 * @author: ryan<zer0131@vip.qq.com>
 * @desc: 基础Model类
 */

namespace onefox;

abstract class Model {

    protected $db;
    protected $dbConfig = 'default';
    protected $table = '';

    public function __construct() {
        $this->db = new DB($this->dbConfig);
    }

    // 生成插入sql
    protected function genInsertSql($data) {
        $columns = array_keys($data);
        $fields = implode(',', $columns);
        $values = ':' . implode(',:', $columns);
        $sql = "insert into {$this->table} ({$fields}) values ({$values})";
        return $sql;
    }

    // 生成update sql
    protected function genUpdateSql($data, $map) {
        $fields = [];
        $where = [];
        $columns = array_keys($data);
        $whereCol = array_keys($map);
        foreach ($columns as $column) {
            $fields[] = $column . '=:' . $column;
        }
        $fieldStr = implode(',', $fields);
        foreach ($whereCol as $val) {
            $where[] = $val.'=:'.$val;
        }
        $whereStr = implode(' and ', $where);
        $sql = "update {$this->table} set {$fieldStr} where {$whereStr}";
        return $sql;
    }

    // 插入新数据, 成功返回最新主键
    protected function insert($data) {
        $sql = $this->genInsertSql($data);
        $r = $this->db->query($sql, $data);
        if ($r) {
            $insertId = $this->db->lastInsertId();
            return $insertId ? $insertId : 0;
        }
        return 0;
    }

}