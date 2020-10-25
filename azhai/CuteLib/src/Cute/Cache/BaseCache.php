<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Cache;

use \SplObserver;
use \SplSubject;
use \Cute\Cache\Subject;

/**
 * 缓存客户端
 */
abstract class BaseCache implements SplObserver
{

    protected $errors = [];

    public function update(SplSubject $subject)
    {
        if (func_num_args() >= 2) {
            $action = func_get_arg(1);
        } else {
            $action = Subject::OP_READ;
        }
        switch ($action) {
            case Subject::OP_READ:
                $data = $subject->get();
                if (is_null($data)) {
                    $subject->set($this->readData());
                }
                break;
            case Subject::OP_WRITE:
                $data = $subject->get();
                $this->writeData($data, $subject->ttl());
                break;
            case Subject::OP_REMOVE:
                $this->removeData();
                break;
        }
    }

    public function isSuccessful()
    {
        return empty($this->errors);
    }

    public function prepare()
    {
        
    }

    abstract public function readData();

    abstract public function writeData($data, $timeout = 0);

    abstract public function removeData();
}
