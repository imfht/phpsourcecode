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
use Purchase\Entity\WarehouseOrder;

class WarehouseOrderRepository extends EntityRepository
{
    /**
     * 查询入库订单
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findWarehouseOrderList($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('w', 'o')
            ->from(WarehouseOrder::class, 'w')
            ->join('w.oneOrder', 'o')
            ->orderBy('w.warehouseOrderState', 'ASC')
            ->addOrderBy('w.warehouseOrderId', 'DESC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    private function querySearchData($search, QueryBuilder $queryBuilder)
    {
        if(isset($search['start_amount']) && $search['start_amount'] > 0)               $queryBuilder->andWhere($queryBuilder->expr()->gte('w.warehouseOrderAmount', $search['start_amount']));
        if(isset($search['end_amount']) && $search['end_amount'] > 0)                   $queryBuilder->andWhere($queryBuilder->expr()->lte('w.warehouseOrderAmount', $search['end_amount']));
        if(isset($search['warehouse_order_sn']) && !empty($search['warehouse_order_sn'])) $queryBuilder->andWhere($queryBuilder->expr()->like('w.warehouseOrderSn', "'%".$search['warehouse_order_sn']."%'"));
        if(isset($search['supplier_contacts']) && !empty($search['supplier_contacts'])) $queryBuilder->andWhere($queryBuilder->expr()->like('o.supplierContacts', "'%".$search['supplier_contacts']."%'"));
        if(isset($search['supplier_phone']) && !empty($search['supplier_phone']))       $queryBuilder->andWhere($queryBuilder->expr()->like('o.supplierPhone', "'%".$search['supplier_phone']."%'"));
        if(isset($search['supplier_id']) && $search['supplier_id'] > 0)                 $queryBuilder->andWhere($queryBuilder->expr()->eq('o.supplierId', $search['supplier_id']));
        if(isset($search['payment_code']) && !empty($search['payment_code']))           $queryBuilder->andWhere($queryBuilder->expr()->eq('o.paymentCode', ':code'))->setParameter('code', $search['payment_code']);
        if(isset($search['p_order_state']) && !empty($search['p_order_state']))         $queryBuilder->andWhere($queryBuilder->expr()->eq('o.pOrderState', $search['p_order_state']));

        return $queryBuilder;
    }
}