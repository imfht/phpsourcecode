<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM\Schema;

use \Cute\ORM\Model;


/**
 * 数据表字段
 */
class Column extends Model
{
    protected static $_keys = [
        'COLUMN_NAME' => 'name', 'COLUMN_DEFAULT' => 'default',
        'COLUMN_KEY' => 'index', 'IS_NULLABLE' => 'nullable',
        'COLUMN_TYPE' => 'column', 'DATA_TYPE' => 'type',
        'CHARACTER_MAXIMUM_LENGTH' => 'length',
        'NUMERIC_PRECISION' => 'precision', 'NUMERIC_SCALE' => 'scale',
        'DATETIME_PRECISION' => 'datetime',
    ];
    public $name = '';
    public $default = null;
    public $index = null;
    public $nullable = 'YES';
    public $column = '';
    public $type = '';
    public $length = null;
    public $precision = null;
    public $scale = null;
    public $datetime = null;

    public static function getTable()
    {
        return 'information_schema.COLUMNS';
    }

    public function __set($prop, $value)
    {
        if (isset(self::$_keys[$prop])) {
            $name = self::$_keys[$prop];
            $this->$name = $value;
        }
    }

    public function isPrimaryKey()
    {
        return $this->index === 'PRI';
    }

    public function isNullable()
    {
        return $this->nullable === 'YES';
    }

    public function getCategory()
    {
        if (!is_null($this->datetime)) {
            return 'datetime';
        } else if (!is_null($this->precision)) {
            return $this->scale > 0 ? 'float' : 'int';
        } else if (!is_null($this->length)) {
            return 'char';
        } else {
            return $this->type;
        }
    }
}
