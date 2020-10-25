<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\session;

use nb\Config;
use nb\Cookie;
use SessionHandler;

/**
 * Driver
 *
 * @package nb\session
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/29
 */
abstract class Driver {

    protected $enable = false;

    abstract public function set($name, $value = '', $prefix = null);

    abstract public function get($name = '', $prefix = null);

    public function __get($name) {
        // TODO: Implement __get() method.

        return $this->get($name);
    }

    public function __set($name, $value) {
        // TODO: Implement __set() method.

        return $this->set($name,$value);
    }

}