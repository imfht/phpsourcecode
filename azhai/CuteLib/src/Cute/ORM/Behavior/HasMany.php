<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM\Behavior;

use \Cute\ORM\Behavior\Relation;
use \Cute\ORM\Database;
use \Cute\Utility\Inflect;


/**
 * 一对多关系
 */
class HasMany extends Relation
{
    protected $foreign_key = '';
    protected $is_unique = false;

    public function __construct($model = '', $foreign_key = '')
    {
        parent::__construct($model);
        $this->foreign_key = $foreign_key;
    }

    public function bind(Database& $db, $table, $joins = null)
    {
        if (empty($this->foreign_key)) {
            $this->foreign_key = Inflect::singularize($table) . '_id';
        }
        return parent::bind($db, $table, $joins);
    }

    public function relative($name, array& $result)
    {
        if (empty($result)) {
            return [];
        }
        $fkey = $this->getForeignKey();
        $values = $this->getAttrs($result);
        $this->queryResult()->combine($values, $fkey, $this->is_unique);
        $default = $this->is_unique ? null : [];
        $this->setAttrs($result, $values, $name, false, $default);
        return $values;
    }

    public function getForeignKey()
    {
        return $this->foreign_key;
    }
}
