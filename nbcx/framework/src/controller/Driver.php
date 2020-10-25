<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\controller;

use nb\Access;
use nb\Controller;

/**
 * Driver
 *
 * @package nb\controller
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/15
 */
abstract class Driver extends Access {

    /**
     * @var Controller
     */
    protected $controller;

    public function __construct(Controller $nb) {
        $this->controller = $nb;
    }

}