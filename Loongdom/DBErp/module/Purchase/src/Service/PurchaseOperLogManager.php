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
use Purchase\Entity\PurchaseOperLog;

class PurchaseOperLogManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加采购操作记录
     * @param array $data
     * @return PurchaseOperLog
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addPurchaseOperLog(array $data)
    {
        $purchaseOrder = new PurchaseOperLog();
        $purchaseOrder->valuesSet($data);

        $this->entityManager->persist($purchaseOrder);
        $this->entityManager->flush();

        return $purchaseOrder;
    }

    /**
     * 删除采购操作记录
     * @param $orderId
     */
    public function delPurchaseOperLog($orderId)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(PurchaseOperLog::class, 'p')->where('p.pOrderId = :pOrderId')->setParameter('pOrderId', $orderId);

        $qb->getQuery()->execute();
    }

    /**
     * 删除采购退货操作记录
     * @param $orderId
     */
    public function delPurchaseReturnOperLog($orderId)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(PurchaseOperLog::class, 'p')
            ->where('p.pOrderId = :pOrderId')->setParameter('pOrderId', $orderId)->andWhere('p.orderState=-1');

        $qb->getQuery()->execute();
    }
}