<?php

/*
 * This file is part of the phpdish/phpdish
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PHPDish\Bundle\AdminBundle\GridFactory;

use PHPDish\Bundle\AdminBundle\Grid\GridInterface;

interface GridFactoryInterface
{
    /**
     * 获取grid
     *
     * @return GridInterface
     */
    public function getGrid();

    /**
     * 获取实体类名称
     *
     * @return string
     */
    public function getEntityClass();
}