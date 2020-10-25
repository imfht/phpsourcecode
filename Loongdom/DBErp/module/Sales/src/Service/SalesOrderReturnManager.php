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
use Sales\Entity\SalesOrderReturn;
use Sales\Entity\SalesSendOrder;

class SalesOrderReturnManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;

    }

    /**
     * 添加销售退货单
     * @param array $data
     * @param SalesSendOrder $sendOrder
     * @param int $adminId
     * @return SalesOrderReturn
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addSalesOrderReturn(array $data, SalesSendOrder $sendOrder, int $adminId)
    {
        $salesOrderReturn = new SalesOrderReturn();
        $salesOrderReturn->setSalesOrderReturnId(null);
        $salesOrderReturn->setSalesOrderId($sendOrder->getSalesOrderId());
        $salesOrderReturn->setSalesOrderSn($sendOrder->getOneSalesOrder()->getSalesOrderSn());
        $salesOrderReturn->setSalesSendOrderId($sendOrder->getSendOrderId());
        $salesOrderReturn->setSalesSendOrderSn($sendOrder->getSendOrderSn());
        $salesOrderReturn->setSalesOrderGoodsReturnAmount(0.0000);
        $salesOrderReturn->setSalesOrderReturnAmount(0.0000);
        $salesOrderReturn->setSalesOrderReturnInfo($data['salesOrderReturnInfo']);
        $salesOrderReturn->setReturnTime(time());
        $salesOrderReturn->setReturnState(-1);
        $salesOrderReturn->setAdminId($adminId);
        $salesOrderReturn->setOneSalesOrder($sendOrder->getOneSalesOrder());

        $this->entityManager->persist($salesOrderReturn);
        $this->entityManager->flush();

        return $salesOrderReturn;
    }

    /**
     * 更新退货状态
     * @param int $state
     * @param SalesOrderReturn $salesOrderReturn
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateSalesOrderReturnState(int $state, SalesOrderReturn $salesOrderReturn, $data = [])
    {
        $salesOrderReturn->setReturnState($state);
        if(isset($data['finishTime'])) $salesOrderReturn->setReturnFinishTime($data['finishTime']);

        $this->entityManager->flush();
    }

    /**
     * 更新退货金额及退货商品金额
     * @param $goodsAmount
     * @param $amount
     * @param SalesOrderReturn $salesOrderReturn
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateSalesOrderReturnAmount($goodsAmount, $amount, SalesOrderReturn $salesOrderReturn)
    {
        $salesOrderReturn->setSalesOrderGoodsReturnAmount($goodsAmount);
        $salesOrderReturn->setSalesOrderReturnAmount($amount);
        $this->entityManager->flush();
    }

    /**
     * 删除销售退货
     * @param SalesOrderReturn $salesOrderReturn
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteSalesOrderReturn(SalesOrderReturn $salesOrderReturn)
    {
        $this->entityManager->remove($salesOrderReturn);
        $this->entityManager->flush();

        return true;
    }
}