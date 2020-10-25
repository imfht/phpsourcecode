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

use PHPDish\Bundle\AdminBundle\Grid\Filter\FilterInterface;

class Column implements ColumnInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var false
     */
    protected $sortable = true;

    /**
     * @var string
     */
    protected $order;

    /**
     * @var false
     */
    protected $filterable = true;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var FilterInterface[]
     */
    protected $filters = [];

    public function __construct($name, $title, $sortable = true, $order = 'DESC')
    {
        $this->name = $name;
        $this->title = $title;
        $this->sortable = $sortable;
        $this->order = $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function isFilterable()
    {
        return $this->filterable;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * {@inheritdoc}
     */
    public function isSortable()
    {
        return $this->sortable;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterByOperator($operator)
    {
        //如果只有一个filter，则operator可以忽略
        if ($operator === null && count($this->filters) === 1) {
            return $this->filters[0];
        }
        foreach ($this->filters as $filter) {
            if ($filter->getOperator() === $operator) {
                return $filter;
            }
        }
        return null;
    }
}