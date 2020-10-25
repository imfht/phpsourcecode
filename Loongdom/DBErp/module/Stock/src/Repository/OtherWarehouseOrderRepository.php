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
use Stock\Entity\OtherWarehouseOrder;

class OtherWarehouseOrderRepository extends EntityRepository
{

    /**
     * 其他入库订单列表
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findOtherWarehouseOrderList($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('o', 'w')
            ->from(OtherWarehouseOrder::class, 'o')
            ->join('o.oneWarehouse', 'w')
            ->orderBy('o.otherWarehouseOrderId', 'DESC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    private function querySearchData($search, QueryBuilder $queryBuilder)
    {
        if(isset($search['warehouse_order_sn']) && !empty($search['warehouse_order_sn']))       $queryBuilder->andWhere($queryBuilder->expr()->like('o.warehouseOrderSn', "'%".$search['warehouse_order_sn']."%'"));
        if(isset($search['start_amount']) && $search['start_amount'] > 0)                       $queryBuilder->andWhere($queryBuilder->expr()->gte('o.warehouseOrderAmount', $search['start_amount']));
        if(isset($search['end_amount']) && $search['end_amount'] > 0)                           $queryBuilder->andWhere($queryBuilder->expr()->lte('o.warehouseOrderAmount', $search['end_amount']));
        if(isset($search['start_time']) && !empty($search['start_time']))                       $queryBuilder->andWhere($queryBuilder->expr()->gte('o.otherAddTime', ':startTime'))->setParameter('startTime', strtotime($search['start_time']));
        if(isset($search['end_time']) && !empty($search['end_time']))                           $queryBuilder->andWhere($queryBuilder->expr()->lte('o.otherAddTime', ':endTime'))->setParameter('endTime', strtotime($search['end_time']));
        if(isset($search['warehouse_id']) && $search['warehouse_id'] > 0)                       $queryBuilder->andWhere($queryBuilder->expr()->eq('o.warehouseId', $search['warehouse_id']));
        if(isset($search['warehouse_order_info']) && !empty($search['warehouse_order_info']))   $queryBuilder->andWhere($queryBuilder->expr()->like('o.warehouseOrderInfo', "'%".$search['warehouse_order_info']."%'"));

        return $queryBuilder;
    }
}