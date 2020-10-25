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
use Store\Entity\Warehouse;
use Store\Entity\WarehouseGoods;

class WarehouseGoodsManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager    = $entityManager;
    }

    /**
     * 添加仓库商品数量记录
     * @param array $data
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addWarehouseGoods(array $data)
    {
        $warehouseInfo = $this->entityManager->getRepository(Warehouse::class)->findOneBy(['warehouseId' => $data['warehouseId']]);
        $warehouseGoods = new WarehouseGoods();
        $warehouseGoods->valuesSet($data);
        /*$warehouseGoods->setWarehouseGoodsId(null);
        $warehouseGoods->setWarehouseId($data['warehouseId']);
        $warehouseGoods->setGoodsId($data['goodsId']);
        $warehouseGoods->setWarehouseGoodsStock($data['goodsStock']);*/
        $warehouseGoods->setOneWarehouse($warehouseInfo);

        $this->entityManager->persist($warehouseGoods);
        $this->entityManager->flush();
    }

    /**
     * 更新仓库商品数量（入库||出库）
     * @param $goodsStock
     * @param WarehouseGoods $warehouseGoods
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateWarehouseGoodsStock($goodsStock, WarehouseGoods $warehouseGoods)
    {
        $warehouseGoods->setWarehouseGoodsStock($goodsStock);
        $this->entityManager->flush();
    }

    /**
     * 更新仓库商品数量（出库）
     * @param array $data
     * @return bool
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function outWarehouseGoodsStock(array $data)
    {
        if(empty($data)) return false;
        foreach ($data as $value) {
            $warehouseGoods = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $value['warehouseId'], 'goodsId' => $value['goodsId']]);
            if(!$warehouseGoods || $warehouseGoods->getWarehouseGoodsStock() < $value['sendNum']) return false;

            $warehouseGoods->setWarehouseGoodsStock($warehouseGoods->getWarehouseGoodsStock() - $value['sendNum']);
            $this->entityManager->flush();
            $this->entityManager->clear(WarehouseGoods::class);
        }
        return true;
    }
}