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
use Purchase\Entity\WarehouseOrderGoods;

class WarehouseOrderGoodsRepository extends EntityRepository
{

    /**
     * 获取更新库存和商品价格的数据
     * @param int $warehouseOrderId
     * @return array
     */
    public function findStockAndPriceData(int $warehouseOrderId)
    {
        $goodsArray = $this->getEntityManager()->createQueryBuilder()
            ->select('g')
            ->from(WarehouseOrderGoods::class, 'g')
            ->where('g.warehouseOrderId='.$warehouseOrderId)
            ->getQuery()->getArrayResult();

        $warehouseGoods = ['warehouseOrderState' => 3];
        foreach ($goodsArray as $goodsValue) {
            $warehouseGoods['goods'][$goodsValue['goodsId']] = [
                'price' => $goodsValue['warehouseGoodsPrice'],
                'num'   => $goodsValue['warehouseGoodsBuyNum']
            ];
        }

        return $warehouseGoods;
    }
}