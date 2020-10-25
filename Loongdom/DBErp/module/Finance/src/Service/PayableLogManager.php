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

use Admin\Entity\AdminUser;
use Doctrine\ORM\EntityManager;
use Finance\Entity\Payable;
use Finance\Entity\PayableLog;

class PayableLogManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加付款记录
     * @param array $data
     * @param Payable $payable
     * @param int $adminId
     * @return PayableLog
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addPayableLog(array $data, Payable $payable, int $adminId)
    {
        $adminInfo = $this->entityManager->getRepository(AdminUser::class)->findOneByAdminId($adminId);

        $payableLog = new PayableLog();
        $data['payLogPaytime'] = strtotime($data['payLogPaytime']);
        $payableLog->valuesSet($data);
        $payableLog->setPayableId($payable->getPayableId());
        $payableLog->setPayLogAddtime(time());
        $payableLog->setAdminId($adminId);
        $payableLog->setOneAdmin($adminInfo);

        if(
            isset($data['payFile']['tmp_name']) &&
            !empty($data['payFile']['tmp_name'])
        ) $payableLog->setPayFile(('/upload/payable/' . basename($data['payFile']['tmp_name'])));
        else $payableLog->setPayFile('');

        $this->entityManager->persist($payableLog);
        $this->entityManager->flush();

        return $payableLog;
    }
}