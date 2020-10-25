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
use Stock\Entity\StockCheckGoods;
use Store\Entity\Goods;
use Store\Entity\WarehouseGoods;

class StockCheckGoodsManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager    = $entityManager;
    }

    /**
     * 添加库存盘点商品
     * @param array $data
     * @param $warehouseId
     * @param $stockCheckId
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addStockCheckGoods(array $data, $warehouseId, $stockCheckId)
    {
        foreach ($data['goodsId'] as $key => $value) {
            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $value]);
            if($goodsInfo) {
                $stockCheckGoods = new StockCheckGoods();
                $stockCheckGoods->setStockCheckId($stockCheckId);
                $stockCheckGoods->setStockCheckAftGoodsNum($data['stockCheckAftGoodsNum'][$key]);
                $stockCheckGoods->setStockCheckGoodsAmount($data['stockCheckGoodsAmount'][$key]);
                $stockCheckGoods->setGoodsId($value);
                $stockCheckGoods->setGoodsName($goodsInfo->getGoodsName());
                $stockCheckGoods->setGoodsNumber($goodsInfo->getGoodsNumber());
                $stockCheckGoods->setGoodsSpec($goodsInfo->getGoodsSpec());
                $stockCheckGoods->setGoodsUnit($goodsInfo->getOneUnit()->getUnitName());

                $warehouseGoodsInfo = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $warehouseId, 'goodsId' => $value]);
                if($warehouseGoodsInfo) $stockCheckGoods->setStockCheckPreGoodsNum($warehouseGoodsInfo->getWarehouseGoodsStock());
                else $stockCheckGoods->setStockCheckPreGoodsNum(0);

                $this->entityManager->persist($stockCheckGoods);
                $this->entityManager->flush();
                $this->entityManager->clear(StockCheckGoods::class);
            }
        }
    }

    /**
     * 编辑盘点商品
     * @param array $data
     * @param int $stockCheckId
     * @param int $warehouseId
     * @return bool
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editStockCheckGoods(array $data, int $stockCheckId, int $warehouseId)
    {
        foreach ($data['goodsId'] as $key => $value) {
            $stockCheckPreGoodsNum = 0;
            $warehouseGoodsInfo = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $warehouseId, 'goodsId' => $value]);
            if($warehouseGoodsInfo) $stockCheckPreGoodsNum = $warehouseGoodsInfo->getWarehouseGoodsStock();

            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $value]);
            $stockCheckGoodsInfo = $this->entityManager->getRepository(StockCheckGoods::class)->findOneBy(['stockCheckId' => $stockCheckId, 'goodsId' => $value]);
            if($stockCheckGoodsInfo) {
                $stockCheckGoodsInfo->setStockCheckPreGoodsNum($stockCheckPreGoodsNum);
                $stockCheckGoodsInfo->setStockCheckAftGoodsNum($data['stockCheckAftGoodsNum'][$key]);
                $stockCheckGoodsInfo->setStockCheckGoodsAmount($data['stockCheckGoodsAmount'][$key]);
            } else {
                if($goodsInfo) {
                    $stockCheckGoods = new StockCheckGoods();
                    $stockCheckGoods->setStockCheckId($stockCheckId);
                    $stockCheckGoods->setStockCheckAftGoodsNum($data['stockCheckAftGoodsNum'][$key]);
                    $stockCheckGoods->setStockCheckGoodsAmount($data['stockCheckGoodsAmount'][$key]);
                    $stockCheckGoods->setGoodsId($value);
                    $stockCheckGoods->setGoodsName($goodsInfo->getGoodsName());
                    $stockCheckGoods->setGoodsNumber($goodsInfo->getGoodsNumber());
                    $stockCheckGoods->setGoodsSpec($goodsInfo->getGoodsSpec());
                    $stockCheckGoods->setGoodsUnit($goodsInfo->getOneUnit()->getUnitName());
                    $stockCheckGoods->setStockCheckPreGoodsNum($stockCheckPreGoodsNum);

                    $this->entityManager->persist($stockCheckGoods);
                }
            }
            $this->entityManager->flush();
            $this->entityManager->clear(StockCheckGoods::class);
        }
        return true;
    }

    /**
     * 删除盘点单商品
     * @param int $stockCheckId
     */
    public function deleteStockCheckIdGoods(int $stockCheckId)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(StockCheckGoods::class, 's')
            ->where('s.stockCheckId = :stockCheckId')->setParameter('stockCheckId', $stockCheckId);

        $qb->getQuery()->execute();
    }

    /**
     * 删除盘点中的某一个商品
     * @param StockCheckGoods $stockCheckGoods
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteStockCheckGoods(StockCheckGoods $stockCheckGoods)
    {
        $this->entityManager->remove($stockCheckGoods);
        $this->entityManager->flush();
    }
}