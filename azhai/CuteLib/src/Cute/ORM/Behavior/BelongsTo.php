<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM\Behavior;

use \Cute\ORM\Behavior\Relation;


/**
 * 一对一或多对一关系
 */
class BelongsTo extends Relation
{
    protected $another_foreign_key = '';

    public function __construct($model = '', $another_foreign_key = '')
    {
        parent::__construct($model);
        $this->another_foreign_key = $another_foreign_key;
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
        $fkey = $this->getAnotherForeignKey($name);
        $values = $this->getAttrs($result, $fkey);
        $this->queryResult()->combine($values, reset($pkeys), true);
        $this->setAttrs($result, $values, $name, $fkey);
        return $values;
    }

    public function getAnotherForeignKey($name)
    {
        if (empty($this->another_foreign_key)) {
            $this->another_foreign_key = $name . '_id';
        }
        return $this->another_foreign_key;
    }
}
