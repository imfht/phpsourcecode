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

namespace Shop\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Shop\Entity\ShopOrder;

class ShopOrderRepository extends EntityRepository
{

    /**
     * 商城的订单sql
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findShopOrderAll($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('o', 'a')
            ->from(ShopOrder::class, 'o')
            ->leftJoin('o.oneApp', 'a')
            ->orderBy('o.shopOrderId', 'DESC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    private function querySearchData($search, QueryBuilder $queryBuilder)
    {
        if(isset($search['order_sn']) && !empty($search['order_sn']))           $queryBuilder->andWhere($queryBuilder->expr()->like('o.shopOrderSn', "'%".$search['order_sn']."%'"));
        if(isset($search['buy_name']) && !empty($search['buy_name']))           $queryBuilder->andWhere($queryBuilder->expr()->like('o.shopBuyName', "'%".$search['buy_name']."%'"));
        if(isset($search['payment_name']) && !empty($search['payment_name']))   $queryBuilder->andWhere($queryBuilder->expr()->like('o.shopPaymentName', "'%".$search['payment_name']."%'"));
        if(isset($search['app_id']) && $search['app_id'] > 0)                   $queryBuilder->andWhere($queryBuilder->expr()->eq('o.appId', $search['app_id']));
        if(isset($search['order_state']) && is_numeric($search['order_state'])) $queryBuilder->andWhere($queryBuilder->expr()->eq('o.shopOrderState', $search['order_state']));
        if(isset($search['start_amount']) && $search['start_amount'] > 0)       $queryBuilder->andWhere($queryBuilder->expr()->gte('o.shopOrderAmount', $search['start_amount']));
        if(isset($search['end_amount']) && $search['end_amount'] > 0)           $queryBuilder->andWhere($queryBuilder->expr()->lte('o.shopOrderAmount', $search['end_amount']));
        if(isset($search['start_time']) && !empty($search['start_time']))       $queryBuilder->andWhere($queryBuilder->expr()->gte('o.shopOrderAddTime', ':startTime'))->setParameter('startTime', strtotime($search['start_time']));
        if(isset($search['end_time']) && !empty($search['end_time']))           $queryBuilder->andWhere($queryBuilder->expr()->lte('o.shopOrderAddTime', ':endTime'))->setParameter('endTime', strtotime($search['end_time']));

        return $queryBuilder;
    }
}