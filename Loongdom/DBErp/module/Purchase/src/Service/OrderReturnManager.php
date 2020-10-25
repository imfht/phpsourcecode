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
use Purchase\Entity\OrderReturn;

class OrderReturnManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加退货单
     * @param array $data
     * @param Order $order
     * @param int $adminId
     * @return OrderReturn
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addOrderReturn(array $data, Order $order, int $adminId)
    {
        $orderReturn = new OrderReturn();
        $orderReturn->setOrderReturnId(null);
        $orderReturn->setPOrderId($order->getPOrderId());
        $orderReturn->setPOrderSn($order->getPOrderSn());
        $orderReturn->setPOrderGoodsReturnAmount(0.0000);
        $orderReturn->setPOrderReturnAmount(0.0000);
        $orderReturn->setPOrderReturnInfo($data['pOrderReturnInfo']);
        $orderReturn->setReturnTime(time());
        $orderReturn->setReturnState(-1);
        $orderReturn->setAdminId($adminId);
        $orderReturn->setOnePOrder($order);

        $this->entityManager->persist($orderReturn);
        $this->entityManager->flush();

        return $orderReturn;
    }

    /**
     * 更新退货状态
     * @param int $state
     * @param OrderReturn $orderReturn
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateOrderReturnState(int $state, OrderReturn $orderReturn, $data = [])
    {
        $orderReturn->setReturnState($state);
        if(isset($data['finishTime'])) $orderReturn->setReturnFinishTime($data['finishTime']);

        $this->entityManager->flush();
    }

    /**
     * 更新退货金额及商品金额
     * @param $goodsAmount
     * @param $amount
     * @param OrderReturn $orderReturn
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateOrderReturnAmount($goodsAmount, $amount, OrderReturn $orderReturn)
    {
        $orderReturn->setPOrderGoodsReturnAmount($goodsAmount);
        $orderReturn->setPOrderReturnAmount($amount);
        $this->entityManager->flush();
    }

    /**
     * 删除退货单
     * @param OrderReturn $orderReturn
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteOrderReturn(OrderReturn $orderReturn)
    {
        $this->entityManager->remove($orderReturn);
        $this->entityManager->flush();

        return true;
    }
}