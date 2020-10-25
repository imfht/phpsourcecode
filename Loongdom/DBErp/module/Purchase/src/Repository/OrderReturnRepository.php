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

namespace Purchase\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Purchase\Entity\OrderReturn;

class OrderReturnRepository extends EntityRepository
{
    /**
     * 采购退货单sql
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findAllOrderReturn($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('o', 'p')
            ->from(OrderReturn::class, 'o')
            ->join('o.onePOrder', 'p')
            ->orderBy('o.orderReturnId', 'DESC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    private function querySearchData($search, QueryBuilder $queryBuilder)
    {
        if(isset($search['start_amount']) && $search['start_amount'] > 0)               $queryBuilder->andWhere($queryBuilder->expr()->gte('o.pOrderReturnAmount', $search['start_amount']));
        if(isset($search['end_amount']) && $search['end_amount'] > 0)                   $queryBuilder->andWhere($queryBuilder->expr()->lte('o.pOrderReturnAmount', $search['end_amount']));
        if(isset($search['p_order_sn']) && !empty($search['p_order_sn'])) $queryBuilder->andWhere($queryBuilder->expr()->like('p.pOrderSn', "'%".$search['p_order_sn']."%'"));
        if(isset($search['supplier_contacts']) && !empty($search['supplier_contacts'])) $queryBuilder->andWhere($queryBuilder->expr()->like('p.supplierContacts', "'%".$search['supplier_contacts']."%'"));
        if(isset($search['supplier_phone']) && !empty($search['supplier_phone']))       $queryBuilder->andWhere($queryBuilder->expr()->like('p.supplierPhone', "'%".$search['supplier_phone']."%'"));
        if(isset($search['supplier_id']) && $search['supplier_id'] > 0)                 $queryBuilder->andWhere($queryBuilder->expr()->eq('p.supplierId', $search['supplier_id']));
        if(isset($search['return_state']) && !empty($search['return_state']))         $queryBuilder->andWhere($queryBuilder->expr()->eq('o.returnState', $search['return_state']));

        return $queryBuilder;
    }
}