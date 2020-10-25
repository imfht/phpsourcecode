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

namespace Admin\Service;

use Admin\Entity\OperLog;
use Doctrine\ORM\EntityManager;

class OperlogManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加操作日志
     * @param array $data
     * @return OperLog
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addOperLog(array $data)
    {
        $oper = new OperLog();
        $oper->valuesSet($data);

        $this->entityManager->persist($oper);
        $this->entityManager->flush();

        return $oper;
    }

    /**
     * 删除
     * @param $clearTime
     */
    public function clearOperLog($clearTime)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(OperLog::class, 'o')->where('o.logTime < :clearTime')->setParameter('clearTime', $clearTime);

        $qb->getQuery()->execute();
    }
}