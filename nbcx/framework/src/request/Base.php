<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\request;

use nb\Pool;

/**
 * Native
 *
 * @package nb\request
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/28
 */
class Base extends Driver {

    public function __construct($fd=null, $reactor_id=null, $data=null) {
        $this->data = $data;
        $this->fd = $fd;
        $this->reactor_id = $reactor_id;
    }

    protected function ___data() {
        trigger_error('property data is read-only');
    }

    /**
     * 获取表单数据，返回一个结果数组
     * @param string $method
     * @param null $args
     * @return array
     */
    public function form(array $args=null) {
        $input = $this->input;
        if($args) {
            $_input = [];
            foreach ($args as $arg) {
                $_input[$arg] = isset($input[$arg])?$input[$arg]:null;
            }
            $input = $_input;
        }
        return $input;
    }

    public function _input() {
        return $this->data;
    }

    public function input($args) {
        if(!is_array($args)) {
            //$this->input(['name','pass']);
            $args = [$args];
        }

        $input = $this->form($args);

        if(is_array($input) === false) {
            return null;
        }

        if(count($input) == 1) {
            return current($input);
        }

        return array_values($input);
    }



}