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

namespace Stock\Service;

use Doctrine\ORM\EntityManager;
use Stock\Entity\OtherWarehouseOrder;
use Store\Entity\Warehouse;

class OtherWarehouseOrderManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager    = $entityManager;
    }

    /**
     * 添加其他入库
     * @param array $data
     * @param array $goodsData
     * @param $adminId
     * @return OtherWarehouseOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addOtherWarehouseOrder(array $data, array $goodsData, $adminId)
    {
        $warehouseInfo = $this->entityManager->getRepository(Warehouse::class)->findOneByWarehouseId($data['warehouseId']);

        $otherWarehouseOrder = new OtherWarehouseOrder();
        $otherWarehouseOrder->valuesSet($data);
        $otherWarehouseOrder->setWarehouseOrderState(3);//入库
        $otherWarehouseOrder->setAdminId($adminId);
        $otherWarehouseOrder->setOtherAddTime(time());
        $otherWarehouseOrder->setOneWarehouse($warehouseInfo);

        $array = ['warehouseOrderGoodsAmount' => 0, 'warehouseOrderTax' => 0, 'warehouseOrderAmount' => 0];
        foreach ($goodsData['goodsId'] as $key => $value) {
            $array['warehouseOrderGoodsAmount'] = $array['warehouseOrderGoodsAmount'] + $goodsData['goodsPrice'][$key] * $goodsData['goodsBuyNum'][$key];
            $array['warehouseOrderAmount']      = $array['warehouseOrderAmount'] + $goodsData['goodsAmount'][$key];
            $array['warehouseOrderTax']         = $array['warehouseOrderTax'] + $goodsData['goodsTax'][$key];
        }
        $otherWarehouseOrder->setWarehouseOrderGoodsAmount($array['warehouseOrderGoodsAmount']);
        $otherWarehouseOrder->setWarehouseOrderAmount($array['warehouseOrderAmount']);
        $otherWarehouseOrder->setWarehouseOrderTax($array['warehouseOrderTax']);

        $this->entityManager->persist($otherWarehouseOrder);
        $this->entityManager->flush();

        return $otherWarehouseOrder;
    }
}