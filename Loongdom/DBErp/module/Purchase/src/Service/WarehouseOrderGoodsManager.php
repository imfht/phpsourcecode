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

namespace Purchase\Service;

use Doctrine\ORM\EntityManager;
use Purchase\Entity\WarehouseOrder;
use Purchase\Entity\WarehouseOrderGoods;

class WarehouseOrderGoodsManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加入库商品
     * @param $orderGoods
     * @param WarehouseOrder $warehouseOrder
     * @return bool
     */
    public function addWarehouseOrderGoods($orderGoods, WarehouseOrder $warehouseOrder)
    {
        //$warehouseGoods = [];
        foreach ($orderGoods as $goodsValue) {
            $warehouseOrderGoods = new WarehouseOrderGoods();
            $warehouseOrderGoods->setWarehouseOrderGoodsId(null);
            $warehouseOrderGoods->setPOrderId($warehouseOrder->getPOrderId());
            $warehouseOrderGoods->setWarehouseOrderId($warehouseOrder->getWarehouseOrderId());
            $warehouseOrderGoods->setWarehouseId($warehouseOrder->getWarehouseId());
            $warehouseOrderGoods->setWarehouseGoodsBuyNum($goodsValue->getPGoodsBuyNum());
            $warehouseOrderGoods->setWarehouseGoodsPrice($goodsValue->getPGoodsPrice());
            $warehouseOrderGoods->setWarehouseGoodsTax($goodsValue->getPGoodsTax());
            $warehouseOrderGoods->setWarehouseGoodsAmount($goodsValue->getPGoodsAmount());
            $warehouseOrderGoods->setGoodsId($goodsValue->getGoodsId());
            $warehouseOrderGoods->setGoodsName($goodsValue->getGoodsName());
            $warehouseOrderGoods->setGoodsNumber($goodsValue->getGoodsNumber());
            $warehouseOrderGoods->setGoodsSpec($goodsValue->getGoodsSpec());
            $warehouseOrderGoods->setGoodsUnit($goodsValue->getGoodsUnit());

            /*$warehouseGoods[] = [
                'warehouseId'   => $warehouseOrder->getWarehouseId(),
                'goodsId'       => $goodsValue->getGoodsId(),
                'goodsStock'    => $goodsValue->getPGoodsBuyNum()
            ];*/

            $this->entityManager->persist($warehouseOrderGoods);
            $this->entityManager->flush();
        }

        return true;
    }
}