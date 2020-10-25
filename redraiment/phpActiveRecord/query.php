<?php

require_once('sqlbuilder.php');
require_once('table.php');

class Query {
    private $table;
    private $sql;

    function __construct($table) {
        $this->table = $table;
        $this->sql = new SqlBuilder();
    }

    public function all(...$parameters) {
        return $this->table->query($this->sql->__toString(), ...$parameters);
    }

    public function one(...$parameters) {
        $this->limit(1);
        $models = $this->all(...$parameters);
        if (!isset($models) || empty($models)) {
            return null;
        } else {
            return $models[0];
        }
    }

    public function select(...$fields) {
        $this->sql->select(...$fields);
        return $this;
    }

    public function from($table) {
        $this->sql->from($table);
        return $this;
    }

    public function join($table) {
        $this->sql->join($table);
        return $this;
    }

    public function leftJoin($table) {
        $this->sql->leftJoin($table);
        return $this;
    }

    public function rightJoin($table) {
        $this->sql->rightJoin($table);
        return $this;
    }

    public function where($condition) {
        $this->sql->addCondition($condition);
        return $this;
    }

    public function groupBy(...$groups) {
        $this->sql->groupBy(...$groups);
        return $this;
    }

    public function having(...$havings) {
        $this->sql->having(...$havings);
        return $this;
    }

    public function orderBy(...$orders) {
        $this->sql->orderBy(...$orders);
        return $this;
    }

    public function limit($limit) {
        $this->sql->limit($limit);
        return $this;
    }

    public function offset($offset) {
        $this->sql->offset($offset);
        return $this;
    }

    public function __toString() {
        return $this->sql->__toString();
    }
}
