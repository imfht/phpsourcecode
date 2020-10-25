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

namespace Stock\Service;

use Doctrine\ORM\EntityManager;
use Stock\Entity\StockCheck;
use Store\Entity\Warehouse;

class StockCheckManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager    = $entityManager;
    }

    /**
     * 添加库存盘点
     * @param array $data
     * @param array $goodsData
     * @param $adminId
     * @return StockCheck
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addStockCheck(array $data, array $goodsData, $adminId)
    {
        $warehouseInfo = $this->entityManager->getRepository(Warehouse::class)->findOneByWarehouseId($data['warehouseId']);

        $stockCheck = new StockCheck();
        $data['stockCheckTime'] = strtotime($data['stockCheckTime']);
        $stockCheck->valuesSet($data);
        $stockCheck->setStockCheckState(2);
        $stockCheck->setAdminId($adminId);
        $stockCheck->setOneWarehouse($warehouseInfo);

        $stockCheckAmount = 0;
        foreach ($goodsData['goodsId'] as $key => $value) {
            $stockCheckAmount = $stockCheckAmount + $goodsData['stockCheckGoodsAmount'][$key];
        }
        $stockCheck->setStockCheckAmount($stockCheckAmount);

        $this->entityManager->persist($stockCheck);
        $this->entityManager->flush();

        return $stockCheck;
    }

    /**
     * 编辑更新库存盘点
     * @param array $data
     * @param array $goodsData
     * @param StockCheck $stockCheck
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateStockCheck(array $data, array $goodsData, StockCheck $stockCheck)
    {
        $data['stockCheckTime'] = strtotime($data['stockCheckTime']);
        $stockCheck->valuesSet($data);

        $stockCheckAmount = 0;
        foreach ($goodsData['goodsId'] as $key => $value) {
            $stockCheckAmount = $stockCheckAmount + $goodsData['stockCheckGoodsAmount'][$key];
        }
        $stockCheck->setStockCheckAmount($stockCheckAmount);

        $this->entityManager->flush();
        return true;
    }

    /**
     * 更新库存盘点状态
     * @param $state
     * @param StockCheck $stockCheck
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateStockCheckState($state, StockCheck $stockCheck)
    {
        $stockCheck->setStockCheckState($state);
        $this->entityManager->flush();
    }

    /**
     * 删除库存盘点
     * @param StockCheck $stockCheck
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteStockCheck(StockCheck $stockCheck)
    {
        $this->entityManager->remove($stockCheck);
        $this->entityManager->flush();
    }
}