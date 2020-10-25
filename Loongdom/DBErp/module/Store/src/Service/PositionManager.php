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
use Store\Entity\Position;
use Store\Entity\Warehouse;

class PositionManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加仓位
     * @param array $data
     * @param int $adminId
     * @return Position
     */
    public function addPosition(array $data, int $adminId)
    {
        $warehouse = $this->entityManager->getRepository(Warehouse::class)->findOneByWarehouseId($data['warehouseId']);

        $position = new Position();
        $position->valuesSet($data);
        $position->setAdminId($adminId);

        $position->setWarehouse($warehouse);

        $this->entityManager->persist($position);
        $this->entityManager->flush();

        return $position;
    }

    /**
     * 更新仓位
     * @param array $data
     * @param Position $position
     * @return bool
     */
    public function updatePosition(array $data, Position $position)
    {
        $warehouse = $this->entityManager->getRepository(Warehouse::class)->findOneByWarehouseId($data['warehouseId']);

        $position->valuesSet($data);

        $position->setWarehouse($warehouse);

        $this->entityManager->flush();

        return true;
    }

    /**
     * 删除仓位
     * @param Position $position
     * @return bool
     */
    public function deletePosition(Position $position)
    {
        $this->entityManager->remove($position);
        $this->entityManager->flush();

        return true;
    }
}