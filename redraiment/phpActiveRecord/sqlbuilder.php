<?php

class SqlBuilder {
    private $mode;

    private $fields;
    private $tables;
    private $conditions;
    private $groups;
    private $havings;
    private $orders;
    private $limit;
    private $offset;

    private function start($mode) {
        $this->mode = $mode;
        $this->fields = [];
        $this->tables = [];
        $this->conditions = [];
        $this->groups = [];
        $this->havings = [];
        $this->orders = [];
        $this->limit = -1;
        $this->offset = -1;
        return $this;
    }

    public function addField($field) {
        $this->fields[] = $field;
        return $this;
    }

    public function addTable($table) {
        $this->tables[] = $table;
        return $this;
    }

    public function addCondition($condition) {
        $this->conditions[] = $condition;
        return $this;
    }

    public function addGroup($group) {
        $this->groups[] = $group;
        return $this;
    }

    public function addHaving($having) {
        $this->havings[] = $having;
        return $this;
    }

    public function addOrder($order) {
        $this->orders[] = $order;
        return $this;
    }

    public function setFields(...$fields) {
        $this->fields = $fields;
        return $this;
    }

    public function setTables(...$tables) {
        $this->tables = $tables;
        return $this;
    }

    public function setConditions(...$conditions) {
        $this->conditions = $conditions;
        return $this;
    }

    public function setGroups(...$groups) {
        $this->groups = $groups;
        return $this;
    }

    public function setHavings(...$havings) {
        $this->havings = $havings;
        return $this;
    }

    public function setOrders(...$orders) {
        $this->orders = $orders;
        return $this;
    }

    public function setLimit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function setOffset($offset) {
        $this->offset = $offset;
        return $this;
    }

    // TSQL

    public function insert() {
        return $this->start('insert');
    }

    public function into($table) {
        return $this->setTables($table);
    }

    public function values(...$fields) {
        return $this->setFields(...$fields);
    }

    public function update($table) {
        $this->start('update');
        return $this->setTables($table);
    }

    public function set(...$fields) {
        return $this->setFields(...$fields);
    }

    public function select(...$fields) {
        $this->start('select');
        if (count($fields) === 0) {
            return $this->setFields('*');
        } else {
            return $this->setFields(...$fields);
        }
    }

    public function delete() {
        return $this->start('delete');
    }

    public function from($table) {
        return $this->setTables($table);
    }

    public function join($table) {
        return $this->addTable('inner join ' . $table);
    }

    public function leftJoin($table) {
        return $this->addTable('left join ' . $table);
    }

    public function rightJoin($table) {
        return $this->addTable('right join ' . $table);
    }

    public function on(...$conditions) {
        $index = count($this->tables) - 1;
        $table = $this->tables[$index];
        $table .= ' on ';
        $table .= implode(' and ', $conditions);
        $this->tables[$index] = $table;
        return $this;
    }

    public function where(...$conditions) {
        return $this->setConditions(...$conditions);
    }

    public function groupBy(...$groups) {
        return $this->setGroups(...$groups);
    }

    public function having(...$havings) {
        return $this->setHavings(...$havings);
    }

    public function orderBy(...$orders) {
        return $this->setOrders(...$orders);
    }

    public function limit($limit) {
        return $this->setLimit($limit);
    }

    public function offset($offset) {
        return $this->setOffset($offset);
    }

    // toString

    private function selectToString() {
        $sql = 'select ';
        $sql .= implode(', ', $this->fields);
        $sql .= ' from ';
        $sql .= implode(' ', $this->tables);
        if (count($this->conditions) > 0) {
            $sql .= ' where ';
            $sql .= implode(' and ', $this->conditions);
        }
        if (count($this->groups) > 0) {
            $sql .= ' group by ';
            $sql .= implode(', ', $this->groups);
            if (count($this->havings) > 0) {
                $sql .= ' having ';
                $sql .= implode(' and ', $this->havings);
            }
        }
        if (count($this->orders) > 0) {
            $sql .= ' order by ';
            $sql .= implode(', ', $this->orders);
        }
        if ($this->limit > 0) {
            $sql .= ' limit ';
            $sql .= $this->limit;
        }
        if ($this->offset > -1) {
            $sql .= ' offset ';
            $sql .= $this->offset;
        }
        return $sql;
    }

    private function insertToString() {
        $sql = "insert into {$this->tables[0]} (";
        $sql .= implode(', ', $this->fields);
        $sql .= ') values (';
        $sql .= implode(', ', array_map(function($field) {
            return '?';
        }, $this->fields));
        $sql .= ')';
        return $sql;
    }

    private function updateToString() {
        $sql = "update {$this->tables[0]} set ";
        $sql .= implode(', ', array_map(function($field) {
            return "{$field} = ?";
        }, $this->fields));
        if (count($this->conditions) > 0) {
            $sql .= ' where ';
            $sql .= implode(' and ', $this->conditions);
        }
        return $sql;
    }

    private function deleteToString() {
        $sql = "delete from {$this->tables[0]}";
        if (count($this->conditions) > 0) {
            $sql .= ' where ';
            $sql .= implode(' and ', $this->conditions);
        }
        return $sql;
    }

    public function __toString() {
        switch ($this->mode) {
        case 'select':
            return $this->selectToString();
            break;
        case 'insert':
            return $this->insertToString();
            break;
        case 'update':
            return $this->updateToString();
            break;
        case 'delete':
            return $this->deleteToString();
            break;
        }
    }
}