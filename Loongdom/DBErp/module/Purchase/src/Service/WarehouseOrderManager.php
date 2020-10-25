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
use Purchase\Entity\Order;
use Purchase\Entity\WarehouseOrder;
use Store\Entity\Warehouse;

class WarehouseOrderManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加入库单
     * @param array $data
     * @param Order $order
     * @param int $adminId
     * @return WarehouseOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addWarehouseOrder(array $data, Order $order, int $adminId)
    {
        $warehouseInfo = $this->entityManager->getRepository(Warehouse::class)->findOneByWarehouseId($data['warehouseId']);

        $warehouseOrder = new WarehouseOrder();
        $warehouseOrder->setWarehouseOrderId(null);
        $warehouseOrder->setWarehouseOrderSn($data['warehouseOrderSn']);
        $warehouseOrder->setWarehouseOrderState($data['warehouseOrderState']);
        $warehouseOrder->setWarehouseOrderInfo($data['warehouseOrderInfo']);
        $warehouseOrder->setWarehouseId($data['warehouseId']);
        $warehouseOrder->setWarehouseOrderAmount($order->getPOrderAmount());
        $warehouseOrder->setWarehouseOrderGoodsAmount($order->getPOrderGoodsAmount());
        $warehouseOrder->setWarehouseOrderTax($order->getPOrderTaxAmount());
        $warehouseOrder->setWarehouseOrderPaymentCode($order->getPaymentCode());
        $warehouseOrder->setPOrderId($order->getPOrderId());
        $warehouseOrder->setAdminId($adminId);
        $warehouseOrder->setOneOrder($order);
        $warehouseOrder->setOneWarehouse($warehouseInfo);

        $this->entityManager->persist($warehouseOrder);
        $this->entityManager->flush();

        return $warehouseOrder;
    }

    /**
     * 删除待入库单
     * @param WarehouseOrder $warehouseOrder
     */
    public function deleteWarehouseOrder(WarehouseOrder $warehouseOrder)
    {
        $this->entityManager->remove($warehouseOrder);
        $this->entityManager->flush();
    }

    /**
     * 更新入库单状态
     * @param int $state
     * @param WarehouseOrder $warehouseOrder
     * @return WarehouseOrder
     */
    public function updateWarehouseOrderState(int $state, WarehouseOrder $warehouseOrder)
    {
        $warehouseOrder->setWarehouseOrderState($state);
        $this->entityManager->flush();

        return $warehouseOrder;
    }
}