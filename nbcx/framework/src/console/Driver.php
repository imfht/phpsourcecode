<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\console;

use nb\Access;
use nb\console\input\Input;
use nb\console\output\Output;

abstract class Driver extends Access {

    abstract public function execute(Input $input, Output $output);

    /**
     * 获取控制台logo
     * @return bool|string
     */
    public function logo() {
       return file_get_contents(__DIR__.DS.'html'.DS.'logo.tpl');
    }
}
