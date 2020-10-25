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

namespace Purchase\Service;

use Doctrine\ORM\EntityManager;
use Purchase\Entity\OrderGoods;
use Purchase\Entity\OrderGoodsReturn;
use Purchase\Entity\OrderReturn;

class OrderGoodsReturnManager
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
     * @param OrderReturn $orderReturn
     * @return array
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addOrderGoodsReturn(array $data, OrderReturn $orderReturn)
    {
        $returnArray = [];
        $returnAmount= 0;
        $goodsReturnAmount = 0;
        foreach ($data['pGoodsId'] as $pGoodsId) {
            $goodsInfo = $this->entityManager->getRepository(OrderGoods::class)->findOneBy(['pGoodsId' => $pGoodsId, 'pOrderId' => $orderReturn->getPOrderId()]);
            if($goodsInfo) {
                $goodsReturn = new OrderGoodsReturn();
                $goodsReturn->setGoodsReturnId(null);
                $goodsReturn->setOrderReturnId($orderReturn->getOrderReturnId());
                $goodsReturn->setPGoodsId($pGoodsId);
                $goodsReturn->setGoodsName($goodsInfo->getGoodsName());
                $goodsReturn->setGoodsNumber($goodsInfo->getGoodsNumber());
                $goodsReturn->setGoodsSpec($goodsInfo->getGoodsSpec());
                $goodsReturn->setGoodsUnit($goodsInfo->getGoodsUnit());
                $goodsReturn->setPGoodsPrice($goodsInfo->getPGoodsPrice());
                $goodsReturn->setPGoodsTax($goodsInfo->getPGoodsTax());
                $goodsReturn->setGoodsReturnNum($data['goodsReturnNum'][$pGoodsId]);
                $goodsReturn->setGoodsReturnAmount($data['goodsReturnAmount'][$pGoodsId]);

                $returnAmount = $returnAmount + $data['goodsReturnAmount'][$pGoodsId];
                $goodsReturnAmount = $goodsReturnAmount + $data['goodsReturnAmount'][$pGoodsId];
                $returnArray['pGoods'][] = [
                    'pOrderId'          =>$orderReturn->getPOrderId(),
                    'pGoodsId'          => $pGoodsId,
                    'goodsReturnNum'    => $data['goodsReturnNum'][$pGoodsId],
                    'goodsReturnAmount' => $data['goodsReturnAmount'][$pGoodsId]
                ];

                $this->entityManager->persist($goodsReturn);
                $this->entityManager->flush();
                $this->entityManager->clear(OrderGoodsReturn::class);
            }
        }

        $returnArray['returnAmount']        = $returnAmount;
        $returnArray['goodsReturnAmount']   = $goodsReturnAmount;

        return $returnArray;
    }

    /**
     * 删除退货商品
     * @param OrderGoodsReturn $orderGoodsReturn
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteOrderGoodsReturn(OrderGoodsReturn $orderGoodsReturn)
    {
        $this->entityManager->remove($orderGoodsReturn);
        $this->entityManager->flush();

        return true;
    }
}