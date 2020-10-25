<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM\Behavior;

use \Cute\ORM\Behavior\NestedSet;


/**
 * Nested节点
 */
trait NestedMixin
{
    public $depth = 0;
    public $parent = null;
    protected $low_value = 0;
    protected $high_value = 0;

    public function getBehaviors()
    {
        return [
            'children' => new NestedSet(__CLASS__),
        ];
    }

    public function isLeaf()
    {
        return $this->getHigh() - $this->getLow() === 1;
    }

    public function getHigh()
    {
        return $this->high_value;
    }

    public function getLow()
    {
        return $this->low_value;
    }

    public function recur($func)
    {
        $func($this);
        foreach ($this->children as $child) {
            $child->recur($func);
        }
    }
}
