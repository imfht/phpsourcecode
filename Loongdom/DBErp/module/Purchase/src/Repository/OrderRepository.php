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
use Purchase\Entity\Order;

class OrderRepository extends EntityRepository
{
    /**
     * 获取采购订单的sql
     * @return \Doctrine\ORM\Query
     */
    public function findAllOrder($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('o', 's')
            ->from(Order::class, 'o')
            ->join('o.oneSupplier', 's')
            ->orderBy('o.pOrderState', 'ASC')
            ->addOrderBy('o.pOrderId', 'DESC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    private function querySearchData($search, QueryBuilder $queryBuilder)
    {
        if(isset($search['start_amount']) && $search['start_amount'] > 0)               $queryBuilder->andWhere($queryBuilder->expr()->gte('o.pOrderAmount', $search['start_amount']));
        if(isset($search['end_amount']) && $search['end_amount'] > 0)                   $queryBuilder->andWhere($queryBuilder->expr()->lte('o.pOrderAmount', $search['end_amount']));
        if(isset($search['p_order_sn']) && !empty($search['p_order_sn']))               $queryBuilder->andWhere($queryBuilder->expr()->like('o.pOrderSn', "'%".$search['p_order_sn']."%'"));
        if(isset($search['supplier_contacts']) && !empty($search['supplier_contacts'])) $queryBuilder->andWhere($queryBuilder->expr()->like('o.supplierContacts', "'%".$search['supplier_contacts']."%'"));
        if(isset($search['supplier_phone']) && !empty($search['supplier_phone']))       $queryBuilder->andWhere($queryBuilder->expr()->like('o.supplierPhone', "'%".$search['supplier_phone']."%'"));
        if(isset($search['supplier_id']) && $search['supplier_id'] > 0)                 $queryBuilder->andWhere($queryBuilder->expr()->eq('o.supplierId', $search['supplier_id']));
        if(isset($search['payment_code']) && !empty($search['payment_code']))           $queryBuilder->andWhere($queryBuilder->expr()->eq('o.paymentCode', ':code'))->setParameter('code', $search['payment_code']);
        if(isset($search['p_order_state']) && is_numeric($search['p_order_state']))     $queryBuilder->andWhere($queryBuilder->expr()->eq('o.pOrderState', $search['p_order_state']));
        if(isset($search['return_state']) && is_numeric($search['return_state']))       $queryBuilder->andWhere($queryBuilder->expr()->eq('o.returnState', $search['return_state']));

        return $queryBuilder;
    }
}