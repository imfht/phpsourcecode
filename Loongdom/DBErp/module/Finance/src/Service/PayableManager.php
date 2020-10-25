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

namespace Finance\Service;

use Doctrine\ORM\EntityManager;
use Finance\Entity\Payable;
use Purchase\Entity\WarehouseOrder;

class PayableManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加付款
     * @param WarehouseOrder $warehouseOrder
     * @return Payable
     */
    public function addPayable(WarehouseOrder $warehouseOrder)
    {
        $Payable = new Payable();
        $Payable->setPayableId(null);
        $Payable->setWarehouseOrderId($warehouseOrder->getWarehouseOrderId());
        $Payable->setPOrderId($warehouseOrder->getPOrderId());
        $Payable->setPOrderSn($warehouseOrder->getOneOrder()->getPOrderSn());
        $Payable->setSupplierId($warehouseOrder->getOneOrder()->getOneSupplier()->getSupplierId());
        $Payable->setSupplierName($warehouseOrder->getOneOrder()->getOneSupplier()->getSupplierName());
        $Payable->setPaymentCode($warehouseOrder->getWarehouseOrderPaymentCode());
        $Payable->setPaymentAmount($warehouseOrder->getWarehouseOrderAmount());
        $Payable->setAdminId($warehouseOrder->getAdminId());
        $Payable->setAddTime(time());
        $finishAmount = $warehouseOrder->getWarehouseOrderPaymentCode() != 'payable' ? $warehouseOrder->getWarehouseOrderAmount() : 0;
        $Payable->setFinishAmount($finishAmount);

        $this->entityManager->persist($Payable);
        $this->entityManager->flush();

        return $Payable;
    }

    /**
     * 更新已付款金额
     * @param $finishAmount
     * @param Payable $payable
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updatePayableFinishAmount($finishAmount, Payable $payable)
    {
        $payable->setFinishAmount($finishAmount);
        $this->entityManager->flush();

        return true;
    }
}