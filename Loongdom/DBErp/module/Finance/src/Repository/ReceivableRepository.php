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

namespace Finance\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Finance\Entity\Receivable;

class ReceivableRepository extends EntityRepository
{
    /**
     * 应收账款列表sql
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findReceivablesList($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('r')
            ->from(Receivable::class, 'r')
            //->where('r.receivableCode = :code')
            //->setParameter('code', 'receivable')
            ->orderBy('r.receivableAmount - r.finishAmount', 'DESC')
            ->addOrderBy('r.receivableId', 'DESC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    /**
     * 对检索信息进行处理
     * @param $search
     * @param QueryBuilder $query
     * @return QueryBuilder
     */
    private function querySearchData($search, QueryBuilder $queryBuilder)
    {
        if(isset($search['sales_start_amount']) && $search['sales_start_amount'] > 0)   $queryBuilder->andWhere($queryBuilder->expr()->gte('r.receivableAmount', $search['sales_start_amount']));
        if(isset($search['sales_end_amount']) && $search['sales_end_amount'] > 0)       $queryBuilder->andWhere($queryBuilder->expr()->lte('r.receivableAmount', $search['sales_end_amount']));
        if(isset($search['start_amount']) && $search['start_amount'] > 0)               $queryBuilder->andWhere($queryBuilder->expr()->gte('r.finishAmount', $search['start_amount']));
        if(isset($search['end_amount']) && $search['end_amount'] > 0)                   $queryBuilder->andWhere($queryBuilder->expr()->lte('r.finishAmount', $search['end_amount']));
        if(isset($search['sales_order_sn']) && !empty($search['sales_order_sn']))       $queryBuilder->andWhere($queryBuilder->expr()->like('r.salesOrderSn', "'%".$search['sales_order_sn']."%'"));
        if(isset($search['customer_name']) && !empty($search['customer_name']))         $queryBuilder->andWhere($queryBuilder->expr()->like('r.customerName', "'%".$search['customer_name']."%'"));
        if(isset($search['receivable_code']) && !empty($search['receivable_code']))     $queryBuilder->andWhere($queryBuilder->expr()->eq('r.receivableCode', ':receivableCode'))->setParameter('receivableCode', $search['receivable_code']);

        return $queryBuilder;
    }
}