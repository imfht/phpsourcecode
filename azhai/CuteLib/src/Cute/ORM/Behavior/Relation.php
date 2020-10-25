<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM\Behavior;

use \Cute\ORM\Database;


/**
 * 关系
 */
abstract class Relation
{
    protected $db = null;
    protected $table = '';
    protected $model = '';
    protected $joins = [];

    public function __construct($model = '')
    {
        $this->model = empty($model) ? '\\Cute\\ORM\\Model' : $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function bind(Database& $db, $table, $joins = null)
    {
        $this->db = $db;
        $this->table = $table;
        if (!empty($joins)) {
            $this->joins = $joins;
        }
        return $this;
    }

    public function queryResult($table = '')
    {
        if (empty($table)) {
            $table = $this->table;
        }
        $result = $this->db->queryModel($this->getModel(), $table);
        if ($this->joins) {
            $result->join($this->joins);
        }
        return $result;
    }

    public function queryMiddle($table)
    {
        return $this->db->queryModel('', $table);
    }

    abstract public function relative($name, array& $result);

    protected function getAttrs(array& $result, $attr = false)
    {
        $attrs = [];
        foreach ($result as &$object) {
            $key = $attr ? $object->$attr : $object->getID();
            $attrs[$key] = null;
        }
        return $attrs;
    }

    protected function setAttrs(array& $result, array& $attrs, $name,
                                $attr = false, $default = null)
    {
        foreach ($result as &$object) {
            $key = $attr ? $object->$attr : $object->getID();
            if (isset($attrs[$key])) {
                $object->$name = &$attrs[$key];
            } else {
                $object->$name = $default;
            }
        }
    }
}
