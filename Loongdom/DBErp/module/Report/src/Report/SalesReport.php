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

namespace Report\Report;

use Doctrine\ORM\EntityManager;
use Sales\Entity\SalesOrder;
use Sales\Entity\SalesOrderGoods;
use Shop\Entity\ShopOrder;
use Shop\Entity\ShopOrderGoods;

class SalesReport
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 下单的用户数
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function salesCustomerCount()
    {
        $customerCount = $this->entityManager->createQueryBuilder()
            ->select('COUNT(DISTINCT(s.customerId))')
            ->from(SalesOrder::class, 's')
            ->getQuery()->getSingleScalarResult();

        return $customerCount ? $customerCount : 0;
    }

    /**
     * 销售订单数
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function salesOrderCount()
    {
        $salesOrderCount = $this->entityManager->createQueryBuilder()
            ->select('COUNT(s.salesOrderId)')
            ->from(SalesOrder::class, 's')
            ->getQuery()->getSingleScalarResult();

        return $salesOrderCount ? $salesOrderCount : 0;
    }

    /**
     * 获取销售订单金额
     * @return int|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function salesAmount()
    {
        $salesAmount = $this->entityManager->createQueryBuilder()
            ->select('SUM(s.salesOrderAmount)')
            ->from(SalesOrder::class, 's')
            ->getQuery()->getSingleScalarResult();

        return $salesAmount ? $salesAmount : 0;
    }

    /**
     * 系统销售的商品数
     * @return int|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function salesGoodsCount()
    {
        $salesGoodsCount = $this->entityManager->createQueryBuilder()
            ->select('COUNT(DISTINCT(g.goodsId))')
            ->from(SalesOrderGoods::class, 'g')
            ->getQuery()->getSingleScalarResult();

        return $salesGoodsCount ? $salesGoodsCount : 0;
    }

    /**
     * 商城下单人数
     * @return int|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function shopBuyUserCount()
    {
        $buyUserCount = $this->entityManager->createQueryBuilder()
            ->select('COUNT(DISTINCT(s.shopBuyName))')
            ->from(ShopOrder::class, 's')
            ->getQuery()->getSingleScalarResult();

        return $buyUserCount ? $buyUserCount : 0;
    }

    /**
     * 商城订单数
     * @return int|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function shopOrderCount()
    {
        $shopOrderCount = $this->entityManager->createQueryBuilder()
            ->select('COUNT(s.shopOrderId)')
            ->from(ShopOrder::class, 's')
            ->getQuery()->getSingleScalarResult();

        return $shopOrderCount ? $shopOrderCount : 0;
    }

    /**
     * 获取商城订单金额
     * @return int|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function shopAmount()
    {
        $shopAmount = $this->entityManager->createQueryBuilder()
            ->select('SUM(s.shopOrderAmount)')
            ->from(ShopOrder::class, 's')
            ->getQuery()->getSingleScalarResult();

        return $shopAmount ? $shopAmount : 0;
    }

    /**
     * 获取商城销售的商品数
     * @return int|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function shopGoodsCount()
    {
        $shopGoodsCount = $this->entityManager->createQueryBuilder()
            ->select('COUNT(DISTINCT(g.goodsName))')
            ->from(ShopOrderGoods::class, 'g')
            ->getQuery()->getSingleScalarResult();

        return $shopGoodsCount ? $shopGoodsCount : 0;
    }
}