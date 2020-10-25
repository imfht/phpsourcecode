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

namespace Store\Repository;

use Doctrine\ORM\EntityRepository;
use Store\Entity\WarehouseGoods;

class WarehouseGoodsRepository extends EntityRepository
{

    /**
     * 仓库商品
     * @param $goodsId
     * @return mixed
     */
    public function findWarehouseGoods($goodsId)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('g', 'w')
            ->from(WarehouseGoods::class, 'g')
            ->leftJoin('g.oneWarehouse', 'w')
            ->where('g.goodsId='.$goodsId)->andWhere('g.warehouseGoodsStock > 0');

        return $query->getQuery()->getResult();
    }

    /**
     * 查询商品数对应的仓库
     * @param $goodsId
     * @param $stockNum
     * @return mixed
     */
    public function findWarehouseStockGoods($goodsId, $stockNum)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('g')
            ->from(WarehouseGoods::class, 'g')
            ->where($query->expr()->eq('g.goodsId', $goodsId))
            ->andWhere($query->expr()->gte('g.warehouseGoodsStock', $stockNum));

        return $query->getQuery()->getResult();
    }

    /**
     * 获取同一个商品在不同仓库的库存
     * @param array $warehouseId
     * @param $goodsId
     * @return int|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findMoreWarehouseGoodsNum(array $warehouseId, $goodsId)
    {
        $query = $this->getEntityManager()->createQuery(
            '
                  SELECT SUM(wg.warehouseGoodsStock) FROM Store\Entity\WarehouseGoods wg WHERE wg.warehouseId IN ('. implode(',', $warehouseId) .')
                  and wg.goodsId='.$goodsId
        );

        $stockNum = $query->getSingleScalarResult();

        return $stockNum ? $stockNum : 0;
    }
}