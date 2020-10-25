<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM\Query;


/**
 * 映射
 */
abstract class BaseSet
{
    protected $model = '';
    protected $name_args = [];
    protected $behaviors = [];
    protected $deep_joins = [];

    public function __construct($model, array $name_args = [])
    {
        if (is_string($model)) {
            $this->model = $model;
        } else if (is_object($model)) {
            $this->model = get_class($model);
        }
        $this->name_args = $name_args;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getTable()
    {
        $table = exec_method_array($this->model, 'getTable');
        return @vsprintf($table, $this->name_args);
    }

    public function getPKey()
    {
        $pkeys = exec_method_array($this->getModel(), 'getPKeys');
        return empty($pkeys) ? null : reset($pkeys);
    }

    public function join($name = '*')
    {
        $model = exec_construct_array($this->getModel());
        $behaviors = $model->getBehaviors();
        $names = is_array($name) ? $name : func_get_args();
        if ($name === '*') {
            $this->behaviors = &$behaviors;
            array_shift($names);
        }
        foreach ($names as $name) {
            if ($pos = strpos($name, '.')) {
                $this->deep_joins[substr($name, 0, $pos)] = substr($name, $pos + 1);
            } else if (isset($behaviors[$name])) {
                $this->behaviors[$name] = &$behaviors[$name];
            }
        }
        return $this;
    }

    /**
     * 获取单个Model，不存在时创建
     */
    public function getOrCreate()
    {
        $object = $this->get(false);
        if (! $object) {
            $model = $this->getModel();
            $object = new $model();
        }
        return $object;
    }

    /**
     * 获取单个Model对象或null
     */
    abstract public function get($id = false, $columns = '*');

    /**
     * 返回Model的数组
     */
    abstract public function all($columns = '*', $combine_style = false);

    /**
     * 按fkey分组，用于外键查询
     */
    abstract public function combine(array& $result, $fkey, $unique = false, $columns = '*');
}
