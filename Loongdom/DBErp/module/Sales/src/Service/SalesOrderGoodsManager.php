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

namespace Sales\Service;

use Doctrine\ORM\EntityManager;
use Sales\Entity\SalesOrderGoods;
use Store\Entity\Goods;

class SalesOrderGoodsManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加销售订单商品
     * @param array $data
     * @param int $salesOrderId
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addSalesOrderGoods(array $data, int $salesOrderId)
    {
        foreach ($data['goodsId'] as $key => $value) {
            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($value);
            if($goodsInfo) {
                $salesOrderGoods = new SalesOrderGoods();
                $salesOrderGoods->setSalesGoodsId(null);
                $salesOrderGoods->setSalesOrderId($salesOrderId);
                $salesOrderGoods->setGoodsId($value);
                $salesOrderGoods->setGoodsName($goodsInfo->getGoodsName());
                $salesOrderGoods->setGoodsNumber($goodsInfo->getGoodsNumber());
                $salesOrderGoods->setGoodsSpec($goodsInfo->getGoodsSpec());
                $salesOrderGoods->setGoodsUnit($goodsInfo->getOneUnit()->getUnitName());
                $salesOrderGoods->setSalesGoodsSellNum($data['salesGoodsSellNum'][$key]);
                $salesOrderGoods->setSalesGoodsPrice($data['salesGoodsPrice'][$key]);
                $salesOrderGoods->setSalesGoodsTax($data['salesGoodsTax'][$key]);
                $salesOrderGoods->setSalesGoodsAmount($data['salesGoodsAmount'][$key]);
                //$salesOrderGoods->setSalesGoodsInfo($data['sales_goods_info'][$key]);

                $this->entityManager->persist($salesOrderGoods);
                $this->entityManager->flush();
                $this->entityManager->clear(SalesOrderGoods::class);
            }
        }
    }

    /**
     * 编辑销售单中的商品
     * @param array $data
     * @param int $salesOrderId
     * @return bool
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editSalesOrderGoods(array $data, int $salesOrderId)
    {
        foreach ($data['goodsId'] as $key => $value) {
            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($value);
            $salesOrderGoodsInfo = $this->entityManager->getRepository(SalesOrderGoods::class)->findOneBy(['salesOrderId' => $salesOrderId, 'goodsId' => $value]);
            if($salesOrderGoodsInfo) {
                $salesOrderGoodsInfo->setSalesGoodsSellNum($data['salesGoodsSellNum'][$key]);
                $salesOrderGoodsInfo->setSalesGoodsPrice($data['salesGoodsPrice'][$key]);
                $salesOrderGoodsInfo->setSalesGoodsTax($data['salesGoodsTax'][$key]);
                $salesOrderGoodsInfo->setSalesGoodsAmount($data['salesGoodsAmount'][$key]);
                $salesOrderGoodsInfo->setGoodsUnit($goodsInfo->getOneUnit()->getUnitName());
            } else {
                if($goodsInfo) {
                    $salesOrderGoods = new SalesOrderGoods();
                    $salesOrderGoods->setSalesGoodsId(null);
                    $salesOrderGoods->setSalesOrderId($salesOrderId);
                    $salesOrderGoods->setGoodsId($value);
                    $salesOrderGoods->setGoodsName($goodsInfo->getGoodsName());
                    $salesOrderGoods->setGoodsNumber($goodsInfo->getGoodsNumber());
                    $salesOrderGoods->setGoodsSpec($goodsInfo->getGoodsSpec());
                    $salesOrderGoods->setGoodsUnit($goodsInfo->getOneUnit()->getUnitName());
                    $salesOrderGoods->setSalesGoodsSellNum($data['salesGoodsSellNum'][$key]);
                    $salesOrderGoods->setSalesGoodsPrice($data['salesGoodsPrice'][$key]);
                    $salesOrderGoods->setSalesGoodsTax($data['salesGoodsTax'][$key]);
                    $salesOrderGoods->setSalesGoodsAmount($data['salesGoodsAmount'][$key]);

                    $this->entityManager->persist($salesOrderGoods);
                }
            }
            $this->entityManager->flush();
            $this->entityManager->clear(SalesOrderGoods::class);
        }
        return true;
    }

    /**
     * 删除销售订单中的商品(单个删除)
     * @param SalesOrderGoods $salesOrderGoods
     * @return bool
     */
    public function deleteSalesOrderGoods(SalesOrderGoods $salesOrderGoods)
    {
        $this->entityManager->remove($salesOrderGoods);
        $this->entityManager->flush();

        return true;
    }

    /**
     * 删除销售单单中的商品(多个删除)
     * @param int $salesOrderId
     */
    public function deleteMoreSalesOrderIdGoods(int $salesOrderId)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(SalesOrderGoods::class, 'g')->where('g.salesOrderId = :salesOrderId')->setParameter('salesOrderId', $salesOrderId);

        $qb->getQuery()->execute();
    }

    /**
     * 退货或者其他调整销售商品总价和数量
     * @param array $data
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateSalesOrderGoodsNumAndAmountSub(array $data)
    {
        if(is_array($data) && !empty($data)) {
            foreach ($data as $value) {
                $salesOrderGoodsInfo = $this->entityManager->getRepository(SalesOrderGoods::class)->findOneBy(['salesOrderId' => $value['salesOrderId'], 'salesGoodsId' => $value['salesGoodsId']]);
                if($salesOrderGoodsInfo) {
                    $salesOrderGoodsInfo->setSalesGoodsSellNum($salesOrderGoodsInfo->getSalesGoodsSellNum() - $value['goodsReturnNum']);
                    $salesOrderGoodsInfo->setSalesGoodsAmount($salesOrderGoodsInfo->getSalesGoodsAmount() - $value['goodsReturnAmount']);

                    $this->entityManager->flush();
                    $this->entityManager->clear(SalesOrderGoods::class);
                }
            }
        }
    }

    /**
     * 取消销售退货，商品返回
     * @param array $data
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateSalesOrderGoodsNumAndAmountAdd(array $data)
    {
        $salesOrderGoodsInfo = $this->entityManager->getRepository(SalesOrderGoods::class)->findOneBy(['salesOrderId' => $data['salesOrderId'], 'salesGoodsId' => $data['salesGoodsId']]);
        if($salesOrderGoodsInfo) {
            $salesOrderGoodsInfo->setSalesGoodsSellNum($salesOrderGoodsInfo->getSalesGoodsSellNum() + $data['goodsReturnNum']);
            $salesOrderGoodsInfo->setSalesGoodsAmount($salesOrderGoodsInfo->getSalesGoodsAmount() + $data['goodsReturnAmount']);

            $this->entityManager->flush();
            $this->entityManager->clear(SalesOrderGoods::class);
        }
    }
}