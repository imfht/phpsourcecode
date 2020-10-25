<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM\Behavior;

use \Cute\ORM\Behavior\HasMany;


/**
 * 一对一关系，外键不在本表
 */
class HasOne extends HasMany
{
    protected $is_unique = true;
}
