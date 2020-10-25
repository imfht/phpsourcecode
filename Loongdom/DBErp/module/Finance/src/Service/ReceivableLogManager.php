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
use Finance\Entity\Receivable;
use Finance\Entity\ReceivableLog;

class ReceivableLogManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加收款记录
     * @param array $data
     * @param Receivable $receivable
     * @param int $adminId
     * @return ReceivableLog
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addReceivableLog(array $data, Receivable $receivable, int $adminId)
    {
        $adminInfo = $this->entityManager->getRepository(AdminUser::class)->findOneByAdminId($adminId);

        $receivableLog = new ReceivableLog();
        $data['receivableLogTime'] = strtotime($data['receivableLogTime']);
        $receivableLog->valuesSet($data);
        $receivableLog->setReceivableId($receivable->getReceivableId());
        $receivableLog->setReceivableAddTime(time());
        $receivableLog->setAdminId($adminId);
        $receivableLog->setOneAdmin($adminInfo);

        if(
            isset($data['receivableFile']['tmp_name']) &&
            !empty($data['receivableFile']['tmp_name'])
        ) $receivableLog->setReceivableFile(('/upload/receivable/' . basename($data['receivableFile']['tmp_name'])));
        else $receivableLog->setReceivableFile('');

        $this->entityManager->persist($receivableLog);
        $this->entityManager->flush();

        return $receivableLog;
    }
}