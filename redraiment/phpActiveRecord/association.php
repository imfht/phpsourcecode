<?php

require_once('utils.php');

class Association {
    private $relations;
    private $onlyOne;
    private $ancestor;

    private $assoc;
    public $target;
    public $alias;
    public $primaryKey;
    public $foreignKey;

    function __construct($relations, $name, $onlyOne, $ancestor) {
        $this->relations = $relations;
        $this->onlyOne = $onlyOne;
        $this->ancestor = $ancestor;

        $this->target = $name;
        $this->alias = null;
        $this->primaryKey = 'id';
        $this->foreignKey = $name . '_id';
        $this->assoc = null;
    }

    public function __get($name) {
        if ($name === 'onlyOne') {
            return $this->onlyOne;
        } elseif ($name === 'ancestor') {
            return $this->ancestor;
        } elseif ($name === 'cross') {
            return $this->assoc !== null;
        }
        return null;
    }

    public function primaryKey($primaryKey) {
        $this->primaryKey = $primaryKey;
        return $this;
    }

    public function via($primaryKey) {
        return $this->primaryKey($primaryKey);
    }

    public function foreignKey($foreignKey) {
        $this->foreignKey = $foreignKey;
        return $this;
    }

    public function by($foreignKey) {
        return $this->foreignKey($foreignKey);
    }

    public function in($table_name) {
        $this->target = $table_name;
        return $this;
    }

    public function alias($alias_name) {
        $this->alias = $alias_name;
        return $this;
    }

    public function be($alias_name) {
        return $this->alias($alias_name);
    }

    public function through($assoc) {
        $assoc = parseKeyParameter($assoc);
        if (isset($this->relations[$assoc])) {
            $this->assoc = $this->relations[$assoc];
        } else {
            throw new Exception("Undefined Association {$assoc}");
        }
        return $this;
    }

    public function assoc($source, $alias, $id, $target_alias = null) {
        if ($this->cross) {
            $other = $this->assoc->assoc($source, $alias, $id, $this->assoc->alias);
            $target_alias = ($this->assoc->alias !== null)? $this->assoc->alias: $this->assoc->target;
            if ($this->ancestor) {
                return "{$this->assoc->target} as {$target_alias} on {$this->target}.{$this->foreignKey} = {$target_alias}.{$this->primaryKey} join {$other}";
            } else {
                return "{$this->assoc->target} as {$target_alias} on {$target_alias}.{$this->foreignKey} = {$this->target}.{$this->primaryKey} join {$other}";
            }
        } else {
            if ($target_alias === null) {
                $target_alias = $this->target;
            }
            if ($this->ancestor) {
                return "{$source} as {$alias} on {$target_alias}.{$this->foreignKey} = {$alias}.{$this->primaryKey} and {$alias}.{$this->primaryKey} = {$id}";
            } else {
                return "{$source} as {$alias} on {$alias}.{$this->foreignKey} = {$target_alias}.{$this->primaryKey} and {$alias}.{$this->primaryKey} = {$id}";
            }
        }
    }
}