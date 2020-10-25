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
use Sales\Entity\SalesOrderGoodsReturn;
use Sales\Entity\SalesOrderReturn;

class SalesOrderGoodsReturnManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加退货商品
     * @param array $data
     * @param SalesOrderReturn $salesOrderReturn
     * @return array
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addSalesOrderGoodsReturn(array $data, SalesOrderReturn $salesOrderReturn)
    {
        $returnArray = [];
        $returnAmount= 0;
        $goodsReturnAmount = 0;
        foreach ($data['salesGoodsId'] as $salesGoodsId) {
            $goodsInfo = $this->entityManager->getRepository(SalesOrderGoods::class)->findOneBy(['salesGoodsId' => $salesGoodsId, 'salesOrderId' => $salesOrderReturn->getSalesOrderId()]);
            if($goodsInfo) {
                $goodsReturn = new SalesOrderGoodsReturn();
                $goodsReturn->setGoodsReturnId(null);
                $goodsReturn->setSalesOrderReturnId($salesOrderReturn->getSalesOrderReturnId());
                $goodsReturn->setSalesGoodsId($goodsInfo->getSalesGoodsId());
                $goodsReturn->setGoodsName($goodsInfo->getGoodsName());
                $goodsReturn->setGoodsNumber($goodsInfo->getGoodsNumber());
                $goodsReturn->setGoodsSpec($goodsInfo->getGoodsSpec());
                $goodsReturn->setGoodsUnit($goodsInfo->getGoodsUnit());
                $goodsReturn->setSalesGoodsPrice($goodsInfo->getSalesGoodsPrice());
                $goodsReturn->setSalesGoodsTax($goodsInfo->getSalesGoodsTax());
                $goodsReturn->setGoodsReturnNum($data['goodsReturnNum'][$salesGoodsId]);
                $goodsReturn->setGoodsReturnAmount($data['goodsReturnAmount'][$salesGoodsId]);

                $returnAmount = $returnAmount + $data['goodsReturnAmount'][$salesGoodsId];
                $goodsReturnAmount = $goodsReturnAmount + $data['goodsReturnAmount'][$salesGoodsId];
                $returnArray['salesGoods'][] = [
                    'salesOrderId'      => $salesOrderReturn->getSalesOrderId(),
                    'salesGoodsId'      => $salesGoodsId,
                    'goodsReturnNum'    => $data['goodsReturnNum'][$salesGoodsId],
                    'goodsReturnAmount' => $data['goodsReturnAmount'][$salesGoodsId]
                ];

                $this->entityManager->persist($goodsReturn);
                $this->entityManager->flush();
                $this->entityManager->clear(SalesOrderGoodsReturn::class);
            }
        }

        $returnArray['returnAmount']        = $returnAmount;
        $returnArray['goodsReturnAmount']   = $goodsReturnAmount;

        return $returnArray;
    }

    /**
     * 删除退货商品
     * @param SalesOrderGoodsReturn $salesOrderGoodsReturn
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteSalesOrderGoodsRetrun(SalesOrderGoodsReturn $salesOrderGoodsReturn)
    {
        $this->entityManager->remove($salesOrderGoodsReturn);
        $this->entityManager->flush();

        return true;
    }
}