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
use Shop\Entity\ShopOrderGoods;

class ShopOrderGoodsRepository extends EntityRepository
{

    /**
     * 商城订单商品sql
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findShopOrderGoodsAll($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('g', 's', 'a')
            ->from(ShopOrderGoods::class, 'g')
            ->leftJoin('g.oneShopOrder', 's')
            ->leftJoin('s.oneApp', 'a')
            ->orderBy('g.orderGoodsId', 'DESC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    private function querySearchData($search, QueryBuilder $queryBuilder)
    {
        if(isset($search['goods_sn']) && !empty($search['goods_sn']))                   $queryBuilder->andWhere($queryBuilder->expr()->like('g.goodsSn', "'%".$search['goods_sn']."%'"));
        if(isset($search['shop_order_sn']) && !empty($search['shop_order_sn']))         $queryBuilder->andWhere($queryBuilder->expr()->like('s.shopOrderSn', "'%".$search['shop_order_sn']."%'"));
        if(isset($search['goods_name']) && !empty($search['goods_name']))               $queryBuilder->andWhere($queryBuilder->expr()->like('g.goodsName', "'%".$search['goods_name']."%'"));
        if(isset($search['goods_spec']) && !empty($search['goods_spec']))               $queryBuilder->andWhere($queryBuilder->expr()->like('g.goodsSpec', "'%".$search['goods_spec']."%'"));
        if(isset($search['start_buy_num']) && $search['start_buy_num'] > 0)             $queryBuilder->andWhere($queryBuilder->expr()->gte('g.buyNum', $search['start_buy_num']));
        if(isset($search['end_buy_num']) && $search['end_buy_num'] > 0)                 $queryBuilder->andWhere($queryBuilder->expr()->lte('g.buyNum', $search['end_buy_num']));
        if(isset($search['order_state']) && $search['order_state'] > 0)                 $queryBuilder->andWhere($queryBuilder->expr()->eq('s.shopOrderState', $search['order_state']));
        if(isset($search['app_id']) && $search['app_id'] > 0)                           $queryBuilder->andWhere($queryBuilder->expr()->eq('s.appId', $search['app_id']));
        if(isset($search['warehouse_id']) && $search['warehouse_id'] > 0)               $queryBuilder->andWhere($queryBuilder->expr()->eq('g.warehouseId', $search['warehouse_id']));
        if(isset($search['distribution_state']) && $search['distribution_state'] > 0)   $queryBuilder->andWhere($queryBuilder->expr()->eq('g.distributionState', $search['distribution_state']));

        return $queryBuilder;
    }
}