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

namespace Admin\Report;

use Customer\Entity\Customer;
use Customer\Entity\Supplier;
use Doctrine\ORM\EntityManager;
use Purchase\Entity\Order;
use Sales\Entity\SalesOrder;
use Store\Entity\Goods;

class HomeReport
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 获取商品总数
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function goodsCount()
    {
        $goodsCount = $this->entityManager->createQueryBuilder()
                ->select('COUNT(g.goodsId) AS goodsCount')
                ->from(Goods::class, 'g')
                ->getQuery()->getSingleScalarResult();

        return $goodsCount ? $goodsCount : 0;
    }

    /**
     * 采购总金额
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function purchaseAmount()
    {
        $amount = $this->entityManager->createQueryBuilder()
                ->select('SUM(p.pOrderAmount) AS orderAmount')
                ->from(Order::class, 'p')
                ->getQuery()->getSingleScalarResult();

        return $amount ? $amount : 0;
    }

    /**
     * 销售金额
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function salesAmount()
    {
        $amount = $this->entityManager->createQueryBuilder()
                ->select('SUM(s.salesOrderAmount)')
                ->from(SalesOrder::class, 's')
                ->getQuery()->getSingleScalarResult();

        return $amount ? $amount : 0;
    }

    /**
     * 客户总数
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function customerCount()
    {
        $count = $this->entityManager->createQueryBuilder()
            ->select('COUNT(c.customerId)')
            ->from(Customer::class, 'c')
            ->getQuery()->getSingleScalarResult();

        return $count ? $count : 0;
    }

    /**
     * 供应商总数
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function supplierCount()
    {
        $count = $this->entityManager->createQueryBuilder()
            ->select('COUNT(s.supplierId)')
            ->from(Supplier::class, 's')
            ->getQuery()->getSingleScalarResult();

        return $count ? $count : 0;
    }

    /**
     * 获取最新采购订单
     * @param int $num
     * @return mixed
     */
    public function purchaseOrderLimit($num = 8)
    {
        return $this->entityManager->createQueryBuilder()
            ->select('p', 'c')
            ->from(Order::class, 'p')
            ->join('p.oneSupplier', 'c')
            ->orderBy('p.pOrderId', 'DESC')
            ->setMaxResults($num)
            ->getQuery()->getResult();
    }

    /**
     * 获取最新销售订单
     * @param int $num
     * @return mixed
     */
    public function salesOrderLimit($num = 8)
    {
        return $this->entityManager->createQueryBuilder()
            ->select('s', 'c')
            ->from(SalesOrder::class, 's')
            ->join('s.oneCustomer', 'c')
            ->orderBy('s.salesOrderId', 'DESC')
            ->setMaxResults($num)
            ->getQuery()->getResult();
    }
}