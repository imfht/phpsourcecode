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
use Sales\Entity\SalesOperLog;

class SalesOperLogManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加销售订单状态记录
     * @param array $data
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addSalesOperLog(array $data)
    {
        $salesOper = new SalesOperLog();
        $salesOper->valuesSet($data);

        $this->entityManager->persist($salesOper);
        $this->entityManager->flush();
    }

    /**
     * 删除订单状态记录
     * @param $orderId
     */
    public function delSalesOperLog($orderId)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(SalesOperLog::class, 's')->where('s.salesOrderId = :salesOrderId')->setParameter('salesOrderId', $orderId);

        $qb->getQuery()->execute();
    }

    /**
     * 删除订单退货状态记录
     * @param $orderId
     */
    public function delSalesReturnOperLog($orderId)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(SalesOperLog::class, 's')
            ->where('s.salesOrderId = :salesOrderId')->setParameter('salesOrderId', $orderId)->andWhere('s.orderState=-1');

        $qb->getQuery()->execute();
    }
}