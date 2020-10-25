<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Contrib\Widget;

use \Cute\Cache\Subject;

/**
 * 全局计数器
 */
class Counter extends Subject
{

    protected $name = '';
    protected $maxium = 0;

    /**
     * 构造函数
     */
    public function __construct($name, $data = 0, $maxium = 0)
    {
        $this->name = $name;
        $this->maxium = intval($maxium);
        $this->set($data);
    }

    public function set($data)
    {
        $data = intval($data);
        return parent::set($data);
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * 自增
     */
    public function increase($step = 1)
    {
        $this->data += $step;
        if ($this->maxium > 0) {
            $this->data = $this->data % $this->maxium;
        }
        $this->write(); //写入缓存
        return $this->data;
    }

}
