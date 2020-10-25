<?php

/*
 * This file is part of the phpdish/phpdish
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PHPDish\Bundle\AdminBundle\Grid\Source;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use PHPDish\Bundle\AdminBundle\Grid\Column\ColumnInterface;
use PHPDish\Bundle\AdminBundle\Grid\Column\JoinColumn;
use PHPDish\Bundle\AdminBundle\Grid\GridInterface;

class ORM implements SourceInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $alias = '_a';

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * 是否已经初始化
     *
     * @var bool
     */
    protected $initialized = false;

    /**
     * @var ClassMetadata
     */
    protected $metadata;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var array
     */
    protected $joins;

    public function __construct($entity, $alias = null)
    {
        $this->entity = $entity;
        $alias && $this->alias = $alias;
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function initialize()
    {
        if ($this->initialized) {
            return;
        }
        $this->metadata = $this->entityManager->getClassMetadata($this->entity);
        $this->class = $this->metadata->getName();
        $this->initialized = true;
    }

    /**
     * {@inheritdoc}
     */
    public function loadSource($columns, $page, $limit)
    {
        $this->initialize();
        $qb = $this->createQueryBuilder();

        $this->applyJoins($qb, $columns);
        $this->applyFilters($qb, $columns);
        $this->applyOrders($qb, $columns);
//        dump($qb->getDQL());
//        dump($qb->getQuery()->getSQL());
//exit;
        $adapter = new DoctrineORMAdapter($qb->getQuery());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setCurrentPage($page)->setMaxPerPage($limit);
        return $pagerfanta;
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder()
    {
        return $this->entityManager->createQueryBuilder()
            ->select($this->alias)
            ->from($this->class, $this->alias);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue($entity, $column)
    {
        $name = $column->getName();
        if ($column instanceof JoinColumn) {

            $associationEntity = $this->getFieldValue($entity, $column->getJoin());
            $name = $column->getName();

        }
        return $this->metadata->getFieldValue($entity, $name);
    }

    /**
     * @param QueryBuilder $qb
     * @param ColumnInterface[] $columns
     */
    protected function applyJoins(QueryBuilder $qb, $columns)
    {
        $this->joins = $this->computeJoins($columns);
        foreach ($this->joins as $alias => $join) {
            if ($join['type'] === 'inner') {
                $func = 'innerJoin';
            } else {
                $func = 'leftJoin';
            }
            $qb->$func($join['join'], $alias);
        }
    }

    /**
     * 计算join类型
     * @param ColumnInterface[] $columns\
     * @return array
     */
    protected function computeJoins($columns)
    {
        $joins = [];
        foreach ($columns as $column) {
            //profile.company
            if ($column instanceof JoinColumn) {
                $joins[$column->getJoin()] = [
                    'alias' => $column->getJoin(),
                    'join' => "{$this->alias}.{$column->getJoin()}",
                    'type' => $column->getJoinType()
                ];
            }
        }
        return $joins;
    }

    /**
     * @param QueryBuilder $qb
     * @param ColumnInterface[] $columns
     */
    protected function applyOrders(QueryBuilder $qb, $columns)
    {
        foreach ($columns as $column) {
            if (!$column->isSortable()) {
                continue;
            }
            //Join Column
            if ($column instanceof JoinColumn) {
                $sort = $this->getFieldName($column->getName(), $column->getJoin());
            } else {
                $sort = $this->getFieldName($column->getName());
            }
            $qb->addOrderBy($sort, $column->getOrder());
        }
    }

    /**
     * @param QueryBuilder $qb
     * @param ColumnInterface[] $columns
     */
    protected function applyFilters(QueryBuilder $qb, $columns)
    {
        $expr = $qb->expr()->andX();
        foreach ($columns as $column) {
            if (!$column->isFilterable()) {
                continue;
            }
            //应用该字段所有的filter
            $exprJunction = $qb->expr()->andX();
            foreach ($column->getFilters() as $filter) {
                if ($filter->shouldSkip()) { //没有设置初值的跳过
                    continue;
                }
                $exprJunction->add($filter->getComparison());
            }
            $expr->add($exprJunction);
        }
        if ($expr->count() > 0) {
            $qb->where($expr);
        }
    }

    /**
     * 获取字段名称
     *
     * @param string $name
     * @param string $join
     * @return string
     */
    protected function getFieldName($name, $join = null)
    {
        if ($join === null) {
            return $this->alias . '.' . $name;
        }
        return $join . '.' . $name;
    }
}