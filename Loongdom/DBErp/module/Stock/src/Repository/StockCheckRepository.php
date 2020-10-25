<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Stock\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Stock\Entity\StockCheck;

class StockCheckRepository extends EntityRepository
{

    /**
     * 库存盘点列表
     * @param $managerId
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findStockCheckList($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('s', 'w')
            ->from(StockCheck::class, 's')
            ->join('s.oneWarehouse', 'w')
            ->orderBy('s.stockCheckId', 'DESC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    private function querySearchData($search, QueryBuilder $queryBuilder)
    {
        return $queryBuilder;
    }
}