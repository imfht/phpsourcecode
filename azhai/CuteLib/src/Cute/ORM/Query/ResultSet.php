<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM\Query;

use \Closure;
use \PDO;
use \PDOStatement;
use \Cute\ORM\Database;
use \Cute\ORM\Query\Builder;
use \Cute\ORM\Query\BaseSet;


/**
 * 映射
 */
class ResultSet extends BaseSet
{
    protected $query = null;
    protected $fetch_style = 0;

    public function __construct(Database& $db, $model, array $name_args = [])
    {
        parent::__construct($model, $name_args);
        $this->setDB($db);
    }
    
    public function __toString()
    {
        return $this->query ? strval($this->query) : '';
    }

    public function setDB(Database& $db)
    {
        $table = $this->getTable();
        $this->query = new Builder($db, $table);
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getFetchStyle()
    {
        if (! $this->fetch_style) {
            $this->fetch_style = PDO::FETCH_CLASS;
            $model = $this->getModel();
            if (method_exists($model, '__set') || method_exists($model, '__construct')) {
                $this->fetch_style = $this->fetch_style | PDO::FETCH_PROPS_LATE;
            }
        }
        return $this->fetch_style;
    }

    /**
     * 分页，支持反向分页
     */
    public function setPage($page_size, $page_no = 1, $total = 0)
    {
        $page_no = intval($page_no); //0表示不分页，负数是反向页码
        if ($page_no < 0 && intval($page_size) > 0) {
            $total = intval($this->count());
        }
        $this->query->setPage($page_size, $page_no, $total);
        return $this;
    }

    /**
     * 获取单个Model对象或null
     */
    public function get($id = false, $columns = '*')
    {
        if ($id !== false) {
            if ($pkey = $this->getPKey()) {
                $this->query->findBy($pkey, $id);
            } else {
                $this->query->setNothing();
            }
        }
        $this->query->setPage(1, 1);
        $stmt = $this->query->select($columns);
        $model = $this->getModel();
        $object = $stmt->fetchObject($model);
        $stmt->closeCursor();
        return $object;
    }

    /**
     * 返回Model的数组
     */
    public function all($columns = '*', $combine_style = false)
    {
        $stmt = $this->query->select($columns);
        $model = $this->getModel();
        $fetch_style = $this->getFetchStyle() | intval($combine_style);
        $result = $stmt->fetchAll($fetch_style, $model);
        $stmt->closeCursor();
        if (is_array($result)) {
            $db = $this->query->getDB();
            $table = $this->getTable();
            foreach ($this->behaviors as $name => &$behavior) {
                $joins = isset($this->deep_joins[$name]) ? $this->deep_joins[$name] : null;
                $behavior->bind($db, $table, $joins)->relative($name, $result);
            }
            return $result;
        }
    }

    /**
     * 按fkey分组，用于外键查询
     */
    public function combine(array& $result, $fkey, $unique = false, $columns = '*')
    {
        if (count($result) === 0 || empty($fkey)) {
            return $result;
        }
        $this->query->findBy($fkey, array_keys($result));
        if ($columns === '*') {
            $db = $this->query->getDB();
            $table_name = $db->getTableName($this->getTable(), true);
            $columns = sprintf('%s, %s.*', $fkey, $table_name);
        }
        $combine_style = $unique ? PDO::FETCH_UNIQUE : PDO::FETCH_GROUP;
        $result = $this->all($columns, $combine_style);
        return $result;
    }

    public function save(& $object, array $keys = null)
    {
        $model = $this->getModel();
        assert($object instanceof $model);
        if ($object->isExists()) {
            $this->findBy($this->getPKey(), $object->getID());
            $data = $object->toArray($keys);
            $this->update($data);
        } else {
            $pkeys = exec_method_array($model, 'getPKeys');
            $data = $object->toArray(null, $pkeys);
            $id = $this->insert($data);
            $object->setID($id);
        }
        return $this;
    }

    public function __call($name, $args)
    {
        if (method_exists($this->query, $name)) {
            exec_method_array($this->query, $name, $args);
            return $this;
        } else {
            var_dump($name, $args);
            $stmt = exec_method_array($this->query, 'apply', $args);
            $column = $stmt->fetchColumn();
            $stmt->closeCursor();
            return $column;
        }
    }
}
