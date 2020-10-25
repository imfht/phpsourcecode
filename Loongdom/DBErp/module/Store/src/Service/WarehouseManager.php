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

class WarehouseManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager    = $entityManager;
    }

    /**
     * 添加仓库
     * @param array $data
     * @param int $adminId
     * @return Warehouse
     */
    public function addWarehouse(array $data, int $adminId)
    {
        $warehouse = new Warehouse();
        $warehouse->valuesSet($data);
        $warehouse->setAdminId($adminId);

        //$warehouse->setUser($user);

        $this->entityManager->persist($warehouse);
        $this->entityManager->flush();

        return $warehouse;
    }

    /**
     * 更新仓库
     * @param Warehouse $warehouse
     * @param array $data
     * @return bool
     */
    public function updateWarehouse(Warehouse $warehouse, array $data)
    {
        $warehouse->valuesSet($data);

        $this->entityManager->flush();

        return true;
    }

    /**
     * 批量处理
     * @param array $data
     */
    public function updateAllWarehouse(array $data)
    {
        foreach ($data['select_id'] as $key => $value) {
            $warehouse = $this->entityManager->getRepository(Warehouse::class)->findOneByWarehouseId($value);

            if($data['editAllState'] == 'sort') {
                $warehouse->setWarehouseSort($data['warehouse_sort'][$value]);
            }

            $this->entityManager->flush();
            $this->entityManager->clear(Warehouse::class);
        }
    }

    /**
     * 删除仓库
     * @param Warehouse $warehouse
     * @return bool
     */
    public function deleteWarehouse(Warehouse $warehouse)
    {
        $position = $this->entityManager->getRepository(Position::class)->findOneBy(['warehouseId' => $warehouse->getWarehouseId()]);
        if($position) return false;

        $this->entityManager->remove($warehouse);
        $this->entityManager->flush();

        return true;
    }
}