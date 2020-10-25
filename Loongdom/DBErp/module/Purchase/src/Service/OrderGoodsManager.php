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
use Store\Entity\Goods;
use Store\Entity\Unit;

class OrderGoodsManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加订单商品
     * @param array $data
     * @param int $orderId
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addOrderGoods(array $data, int $orderId)
    {
        foreach ($data['goodsId'] as $key => $value) {
            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($value);
            if($goodsInfo) {
                $orderGoods = new OrderGoods();
                $orderGoods->setPGoodsId(null);
                $orderGoods->setPOrderId($orderId);
                $orderGoods->setGoodsId($value);
                $orderGoods->setGoodsName($goodsInfo->getGoodsName());
                $orderGoods->setGoodsNumber($goodsInfo->getGoodsNumber());
                $orderGoods->setGoodsSpec($goodsInfo->getGoodsSpec());
                $orderGoods->setGoodsUnit($goodsInfo->getOneUnit()->getUnitName());
                $orderGoods->setPGoodsBuyNum($data['goodsBuyNum'][$key]);
                $orderGoods->setPGoodsPrice(floatval($data['goodsPrice'][$key]));
                $orderGoods->setPGoodsAmount(floatval($data['goodsAmount'][$key]));
                $orderGoods->setPGoodsTax(floatval($data['goodsTax'][$key]));
                //$orderGoods->setPGoodsInfo($data['goods_info'][$key]);

                $this->entityManager->persist($orderGoods);
                $this->entityManager->flush();
                $this->entityManager->clear(OrderGoods::class);
            }
        }
    }

    /**
     * 编辑订单商品
     * @param array $data
     * @param int $orderId
     * @return bool
     */
    public function editOrderGoods(array $data, int $orderId)
    {
        foreach ($data['goodsId'] as $key => $value) {
            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($value);
            $orderGoodsInfo = $this->entityManager->getRepository(OrderGoods::class)->findOneBy(['pOrderId' => $orderId, 'goodsId' => $value]);
            if($orderGoodsInfo) {
                $orderGoodsInfo->setPGoodsBuyNum($data['goodsBuyNum'][$key]);
                $orderGoodsInfo->setPGoodsPrice(floatval($data['goodsPrice'][$key]));
                $orderGoodsInfo->setPGoodsAmount(floatval($data['goodsAmount'][$key]));
                $orderGoodsInfo->setPGoodsTax(floatval($data['goodsTax'][$key]));
                $orderGoodsInfo->setGoodsUnit($goodsInfo->getOneUnit()->getUnitName());
            } else {
                if($goodsInfo) {
                    $orderGoods = new OrderGoods();
                    $orderGoods->setPGoodsId(null);
                    $orderGoods->setPOrderId($orderId);
                    $orderGoods->setGoodsId($value);
                    $orderGoods->setGoodsName($goodsInfo->getGoodsName());
                    $orderGoods->setGoodsNumber($goodsInfo->getGoodsNumber());
                    $orderGoods->setGoodsSpec($goodsInfo->getGoodsSpec());
                    $orderGoods->setGoodsUnit($goodsInfo->getOneUnit()->getUnitName());
                    $orderGoods->setPGoodsBuyNum($data['goodsBuyNum'][$key]);
                    $orderGoods->setPGoodsPrice(floatval($data['goodsPrice'][$key]));
                    $orderGoods->setPGoodsAmount(floatval($data['goodsAmount'][$key]));
                    $orderGoods->setPGoodsTax(floatval($data['goodsTax'][$key]));

                    $this->entityManager->persist($orderGoods);
                }
            }
            $this->entityManager->flush();
            $this->entityManager->clear(OrderGoods::class);
        }
        return true;
    }

    /**
     * 删除同一采购单下面的商品
     * @param int $orderId
     */
    public function deleteMoreOrderIdGoods(int $orderId)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(OrderGoods::class, 'g')->where('g.pOrderId = :pOrderId')->setParameter('pOrderId', $orderId);

        $qb->getQuery()->execute();
    }

    /**
     * 退货或者其他调整采购商品总价和数量
     * @param array $data
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateOrderGoodsBuyNumAndAmountSub(array $data)
    {
        if(is_array($data) && !empty($data)) {
            foreach ($data as $value) {
                $orderGoodsInfo = $this->entityManager->getRepository(OrderGoods::class)->findOneBy(['pOrderId' => $value['pOrderId'], 'pGoodsId' => $value['pGoodsId']]);
                if($orderGoodsInfo) {
                    $orderGoodsInfo->setPGoodsBuyNum($orderGoodsInfo->getPGoodsBuyNum() - $value['goodsReturnNum']);
                    $orderGoodsInfo->setPGoodsAmount($orderGoodsInfo->getPGoodsAmount() - $value['goodsReturnAmount']);

                    $this->entityManager->flush();
                    $this->entityManager->clear(OrderGoods::class);
                }
            }
        }
    }

    /**
     * 取消退货，商品返回
     * @param array $data
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateOrderGoodsBuyNumAndAmountAdd(array $data)
    {
        $orderGoodsInfo = $this->entityManager->getRepository(OrderGoods::class)->findOneBy(['pOrderId' => $data['pOrderId'], 'pGoodsId' => $data['pGoodsId']]);
        if($orderGoodsInfo) {
            $orderGoodsInfo->setPGoodsBuyNum($orderGoodsInfo->getPGoodsBuyNum() + $data['goodsReturnNum']);
            $orderGoodsInfo->setPGoodsAmount($orderGoodsInfo->getPGoodsAmount() + $data['goodsReturnAmount']);

            $this->entityManager->flush();
            $this->entityManager->clear(OrderGoods::class);
        }
    }

    /**
     * 删除采购单商品
     * @param OrderGoods $orderGoods
     * @return bool
     */
    public function deleteOrderGoods(OrderGoods $orderGoods)
    {
        $this->entityManager->remove($orderGoods);
        $this->entityManager->flush();

        return true;
    }
}