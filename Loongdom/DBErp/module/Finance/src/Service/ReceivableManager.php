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
use Finance\Entity\Receivable;
use Sales\Entity\SalesSendOrder;

class ReceivableManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加收款信息
     * @param SalesSendOrder $salesSendOrder
     * @return Receivable
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addReceivable(SalesSendOrder $salesSendOrder)
    {
        $receivable = new Receivable();
        $receivable->setReceivableId(null);
        $receivable->setSalesOrderId($salesSendOrder->getSalesOrderId());
        $receivable->setSalesOrderSn($salesSendOrder->getOneSalesOrder()->getSalesOrderSn());
        $receivable->setSendOrderId($salesSendOrder->getSendOrderId());
        $receivable->setSendOrderSn($salesSendOrder->getSendOrderSn());
        $receivable->setCustomerId($salesSendOrder->getOneSalesOrder()->getCustomerId());
        $receivable->setCustomerName($salesSendOrder->getOneSalesOrder()->getOneCustomer()->getCustomerName());
        $receivable->setReceivableCode($salesSendOrder->getOneSalesOrder()->getReceivablesCode());
        $receivable->setReceivableAmount($salesSendOrder->getOneSalesOrder()->getSalesOrderAmount());
        $finishAmount = $salesSendOrder->getOneSalesOrder()->getReceivablesCode() != 'receivable' ? $salesSendOrder->getOneSalesOrder()->getSalesOrderAmount() : 0;
        $receivable->setFinishAmount($finishAmount);
        $receivable->setAddTime(time());
        $receivable->setAdminId($salesSendOrder->getAdminId());

        $this->entityManager->persist($receivable);
        $this->entityManager->flush();

        return $receivable;
    }

    /**
     * 更新收款金额
     * @param $finishAmount
     * @param Receivable $receivable
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateReceivableFinishAmount($finishAmount, Receivable $receivable)
    {
        $receivable->setFinishAmount($finishAmount);
        $this->entityManager->flush();

        return true;
    }
}