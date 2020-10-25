<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\console\output\table;

class Separator extends Cell {

    public function __construct(array $options = []) {
        parent::__construct('', $options);
    }
}
