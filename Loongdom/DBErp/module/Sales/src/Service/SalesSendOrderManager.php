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

namespace Sales\Service;

use Doctrine\ORM\EntityManager;
use Sales\Entity\SalesOrder;
use Sales\Entity\SalesSendOrder;

class SalesSendOrderManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加发货销售单
     * @param array $data
     * @param SalesOrder $salesOrder
     * @param int $adminId
     * @return SalesSendOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addSalesSendOrder(array $data, SalesOrder $salesOrder, int $adminId)
    {
        $sendOrder = new SalesSendOrder();
        $sendOrder->setSendOrderId(null);
        $sendOrder->setSendOrderSn($data['sendOrderSn']);
        $sendOrder->setSalesOrderId($salesOrder->getSalesOrderId());
        $sendOrder->setReturnState(0);
        $sendOrder->setAdminId($adminId);
        $sendOrder->setOneSalesOrder($salesOrder);

        $this->entityManager->persist($sendOrder);
        $this->entityManager->flush();

        return $sendOrder;
    }

    /**
     * 修改发货订单是否有退货
     * @param $state
     * @param SalesSendOrder $sendOrder
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateSalesSendOrderReturnState($state, SalesSendOrder $sendOrder)
    {
        $sendOrder->setReturnState($state);
        $this->entityManager->flush();

        return true;
    }
}