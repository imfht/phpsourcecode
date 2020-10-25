<?php

require_once('utils.php');
require_once('db.php');
require_once('association.php');
require_once('query.php');
require_once('record.php');

class Table {
    private $db;
    private $name;
    private $columns;
    private $relations;
    private $hooks;
    private $primaryKey;
    private $foreignKeys;
    private $foreignTableName;

    function __construct($db, $name, $columns, &$relations, &$hooks) {
        $this->db = $db;
        $this->name = $name;
        $this->columns = $columns;
        $this->relations = &$relations;
        $this->hooks = &$hooks;
        $this->primaryKey = $name . ".id";
        $this->foreignKeys = [];
    }

    public function extend($hooks) {
        foreach ($hooks as $method => $fn) {
            if (preg_match('/^(?:get|set|call)_/', $method)) {
                $this->hooks[$method] = $fn;
            }
        }
        return $this;
    }

    public function __get($name) {
        if (in_array($name, array('db', 'name', 'columns', 'relations', 'hooks'))) {
            return $this->$name;
        }
        return null;
    }

    /* Association */
    private function assoc($name, $onlyOne, $ancestor) {
        $assoc = new Association($this->relations, $name, $onlyOne, $ancestor);
        $this->relations[$name] = $assoc;
        return $assoc;
    }

    public function belongsTo($name) {
        return $this->assoc($name, true, false);
    }

    public function hasOne($name) {
        return $this->assoc($name, true, true);
    }

    public function hasMany($name) {
        return $this->assoc($name, false, true);
    }

    public function hasAndBelongsToMany($name) {
        return $this->assoc($name, false, false);
    }

    private function getForeignKeys() {
        return array_map(function($key) {
            return "{$this->name}.{$key} = {$this->foreignKeys[$key]}";
        }, array_keys($this->foreignKeys));
    }

    public function constrain($key, $id) {
        $this->foreignKeys[parseKeyParameter($key)] = $id;
        return $this;
    }

    public function join($table) {
        $this->foreignTableName = $table;
        return $this;
    }

    /* CRUD */
    public function create(...$args) {
        $columns = array_keys($this->foreignKeys);
        $values = array_values($this->foreignKeys);
        for ($i = 0; $i < func_num_args(); $i += 2) {
            $key = parseKeyParameter($args[$i]);
            if (in_array($key, $this->columns)) {
                $columns[] = $key;
                $values[] = $args[$i + 1];
            }
        }
        $sql = new SqlBuilder();
        $sql->insert()->into($this->name)->values(...$columns);
        $this->db->execute($sql->__toString(), ...$values);
        $id = $this->db->lastInsertId($this->name);
        return $id > 0? $this->find($id): null;
    }

    public function update($record) {
        $sql = "update {$this->name} set ";
        $sql .= implode(", ", array_map(function($column) {
            return "{$column} = ?";
        }, $this->columns));
        $sql .= ", updated_at = now() where id = {$record->id}";
        $values = array_map(function($column) use($record) {
            return $record->$column;
        }, $this->columns);
        return $this->db->execute($sql, ...$values);
    }

    public function delete($record) {
        $sql = new SqlBuilder();
        $sql->delete()->from($this->name)->where("{$this->primaryKey} = {$record->id}");
        $this->db->execute($sql->__toString());
    }

    public function purge() {
        foreach ($this->all() as $record) {
            $this->delete($record);
        }
    }

    public function query($sql, ...$parameters) {
        return array_map(function($row) {
            return new Record($this, $row);
        }, $this->db->query($sql, ...$parameters));
    }

    public function select(...$fields) {
        $sql = new Query($this);
        if (func_num_args() === 0) {
            $sql->select("{$this->name}.*");
        } else {
            $sql->select(...$fields);
        }
        $sql->from($this->name);
        if (!empty($this->foreignTableName)) {
            $sql->join($this->foreignTableName);
        }
        foreach ($this->getForeignKeys() as $condition) {
            $sql->where($condition);
        }
        if (func_num_args() === 0 || in_array($this->primaryKey, $fields) || in_array('id', $fields)) {
            $sql->orderBy($this->primaryKey);
        }
        return $sql;
    }

    public function first(...$args) {
        if (func_num_args() === 0) {
            return $this->select()->limit(1)->one();
        } else {
            $condition = array_shift($args);
            $query = $this->select()->where($condition)->limit(1);
            return $query->one(...$args);
        }
    }

    public function last(...$args) {
        if (func_num_args() === 0) {
            return $this->select()->orderBy("{$this->primaryKey} desc")->limit(1)->one();
        } else {
            $condition = array_shift($args);
            $query = $this->select()->where($condition)->orderBy("{$this->primaryKey} desc")->limit(1);
            return $query->one(...$args);
        }
    }

    public function find($id) {
        return $this->first("{$this->primaryKey} = ?", $id);
    }

    public function findA($key, $value) {
        $key = parseKeyParameter($key);
        if (is_null($value)) {
            return $this->first("{$key} is null");
        } else {
            return $this->first("{$key} = ?", $value);
        }
    }

    public function findBy($key, $value) {
        $key = parseKeyParameter($key);
        if (is_null($value)) {
            return $this->where("{$key} is null");
        } else {
            return $this->where("{$key} = ?", $value);
        }
    }

    public function all() {
        return $this->select()->all();
    }

    public function where($condition, ...$args) {
        $query = $this->select()->where($condition);
        return $query->all(...$args);
    }

    public function paging($page, $size) {
        return $this->select()->limit($size)->offset(($page - 1) * $size)->all();
    }

    public function size(...$args) {
        $query = $this->select('count(*) as size');
        if (func_num_args() === 0) {
            $record = $query->one();
        } else {
            $condition = array_shift($args);
            $record = $query->where($condition)->one(...$args);
        }
        return intval($record->size);
    }
}
