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

namespace Store\Service;

use Doctrine\ORM\EntityManager;
use Store\Entity\Unit;

class UnitManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加单位
     * @param array $data
     * @param int $adminId
     * @return Unit
     */
    public function addUnit(array $data, int $adminId)
    {
        $unit = new Unit();
        $unit->valuesSet($data);
        $unit->setAdminId($adminId);

        $this->entityManager->persist($unit);
        $this->entityManager->flush();

        return $unit;
    }

    /**
     * 编辑更新单位
     * @param array $data
     * @param Unit $unit
     * @return bool
     */
    public function updateUnit(array $data, Unit $unit)
    {
        $unit->valuesSet($data);

        $this->entityManager->flush();

        return true;
    }

    /**
     * 删除单位
     * @param Unit $unit
     * @return bool
     */
    public function deleteUnit($unit)
    {
        $this->entityManager->remove($unit);
        $this->entityManager->flush();

        return true;
    }
}