<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM;

use \DateTime;


/**
 * 字面量，不被转义
 */
class Literal
{
    protected $value;

    public function __construct($value)
    {
        if (is_null($value)) {
            $this->value = 'NULL';
        } else {
            $this->value = strval($value);
        }
    }

    public function __toString()
    {
        return $this->value;
    }
}
