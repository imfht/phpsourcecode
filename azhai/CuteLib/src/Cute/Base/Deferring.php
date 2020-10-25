<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Base;


/**
 * 可析构类
 */
trait Deferring
{
    protected $closed = false;

    /**
     * 构造函数
     */
    public function defer()
    {
        register_shutdown_function(function ($self) {
            $self->__destruct();
        }, $this);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        if ($this->closed === false) {
            $this->close();
        }
        $this->closed = true;
    }

    /**
     * 析构过程
     */
    public function close()
    {
    }
}
