<?php

/*
 * This file is part of the phpdish/phpdish
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */


namespace PHPDish\Bundle\AdminBundle\Grid\Column;

class JoinColumn extends Column
{
    /**
     * @var string
     */
    protected $joinType = 'INNER JOIN';

    /**
     * @var string
     */
    protected $join;

    public function __construct($name, $title, $join, $joinType = null, bool $sortable = true, string $order = 'DESC')
    {
        parent::__construct($name, $title, $sortable, $order);
        $this->join = $join;
        $joinType && $this->joinType = $joinType;
    }

    /**
     * @return string
     */
    public function getJoinType(): string
    {
        return $this->joinType;
    }

    /**
     * @return string
     */
    public function getJoin(): string
    {
        return $this->join;
    }
}