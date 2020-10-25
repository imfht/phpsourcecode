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
use Finance\Entity\Payable;

class PayableRepository extends EntityRepository
{

    /**
     * 应付账款
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findPayableList($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from(Payable::class, 'a')
            //->where('a.paymentCode = :code')
            //->setParameter('code', 'payable')
            ->orderBy('a.paymentAmount - a.finishAmount', 'DESC')
            ->addOrderBy('a.payableId', 'DESC');

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
        if(isset($search['pur_start_amount']) && $search['pur_start_amount'] > 0)   $queryBuilder->andWhere($queryBuilder->expr()->gte('a.paymentAmount', $search['pur_start_amount']));
        if(isset($search['pur_end_amount']) && $search['pur_end_amount'] > 0)       $queryBuilder->andWhere($queryBuilder->expr()->lte('a.paymentAmount', $search['pur_end_amount']));
        if(isset($search['start_amount']) && $search['start_amount'] > 0)           $queryBuilder->andWhere($queryBuilder->expr()->gte('a.finishAmount', $search['start_amount']));
        if(isset($search['end_amount']) && $search['end_amount'] > 0)               $queryBuilder->andWhere($queryBuilder->expr()->lte('a.finishAmount', $search['end_amount']));
        if(isset($search['p_order_sn']) && !empty($search['p_order_sn']))           $queryBuilder->andWhere($queryBuilder->expr()->like('a.pOrderSn', "'%".$search['p_order_sn']."%'"));
        if(isset($search['supplier_name']) && !empty($search['supplier_name']))     $queryBuilder->andWhere($queryBuilder->expr()->like('a.supplierName', "'%".$search['supplier_name']."%'"));
        if(isset($search['payment_code']) && !empty($search['payment_code']))       $queryBuilder->andWhere($queryBuilder->expr()->eq('a.paymentCode', ':paymentCode'))->setParameter('paymentCode', $search['payment_code']);

        return $queryBuilder;
    }
}