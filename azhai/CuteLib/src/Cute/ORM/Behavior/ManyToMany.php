<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM\Behavior;

use \Cute\ORM\Behavior\HasMany;


/**
 * 多对多关系
 */
class ManyToMany extends HasMany
{
    protected $another_foreign_key = '';
    protected $middle_table = '';

    public function __construct($model = '', $foreign_key = '',
                                $another_foreign_key = '', $middle_table = '')
    {
        parent::__construct($model, $foreign_key);
        $this->another_foreign_key = $another_foreign_key;
        $this->middle_table = $middle_table;
    }

    public function relative($name, array& $result)
    {
        if (empty($result)) {
            return [];
        }
        $pkeys = exec_method_array($this->model, 'getPKeys');
        if (empty($pkeys)) {
            return [];
        }

        $fkey = $this->getForeignKey();
        $values = $this->getAttrs($result);
        $this->queryMiddle($this->middle_table)->combine($values, $fkey, false);
        $an_fkey = $this->getAnotherForeignKey($name);
        $another_values = [];
        foreach ($values as $key => $value) {
            foreach ($value as $k => $val) {
                $index = $val->$an_fkey;
                $values[$key][$k] = $index;
                $another_values[$index] = null;
            }
        }
        $this->queryResult()->combine($another_values, reset($pkeys), true);
        foreach ($result as &$object) {
            $key = $object->getID();
            if (!isset($values[$key])) {
                continue;
            }
            $objs = [];
            foreach ($values[$key] as $index) {
                $objs[] = &$another_values[$index];
            }
            $object->$name = $objs;
        }
        return $another_values;
    }

    public function getAnotherForeignKey($name)
    {
        if (empty($this->another_foreign_key)) {
            $this->another_foreign_key = $name . '_id';
        }
        return $this->another_foreign_key;
    }
}
