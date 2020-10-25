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

use Customer\Entity\Customer;
use Doctrine\ORM\EntityManager;
use Sales\Entity\SalesOrder;

class SalesOrderManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;

    }

    /**
     * 添加销售订单
     * @param array $data
     * @param array $goodsData
     * @param int $adminId
     * @return SalesOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addSalesOrder(array $data, array $goodsData, int $adminId)
    {
        $customerInfo = $this->entityManager->getRepository(Customer::class)->findOneByCustomerId($data['customerId']);

        $salesOrder = new SalesOrder();
        $salesOrder->valuesSet($data);

        $salesOrder->setSalesOrderState(0);
        $salesOrder->setReturnState(0);
        $salesOrder->setAdminId($adminId);
        $salesOrder->setOneCustomer($customerInfo);

        $array = ['salesOrderGoodsAmount' => 0, 'salesOrderTaxAmount' => 0, 'salesOrderAmount' => 0];
        foreach ($goodsData['goodsId'] as $key => $value) {
            $array['salesOrderGoodsAmount']  = $array['salesOrderGoodsAmount'] + $goodsData['salesGoodsPrice'][$key] * $goodsData['salesGoodsSellNum'][$key];
            $array['salesOrderTaxAmount']    = $array['salesOrderTaxAmount'] + $goodsData['salesGoodsTax'][$key];
            $array['salesOrderAmount']        = $array['salesOrderAmount'] + $goodsData['salesGoodsAmount'][$key];
        }

        $salesOrder->setSalesOrderGoodsAmount($array['salesOrderGoodsAmount']);
        $salesOrder->setSalesOrderTaxAmount($array['salesOrderTaxAmount']);
        $salesOrder->setSalesOrderAmount($array['salesOrderAmount']);

        $this->entityManager->persist($salesOrder);
        $this->entityManager->flush();

        return $salesOrder;
    }

    /**
     * 编辑更新销售订单
     * @param array $data
     * @param array $goodsData
     * @param SalesOrder $salesOrder
     * @return bool
     */
    public function updateSalesOrder(array $data, array $goodsData, SalesOrder $salesOrder)
    {
        $salesOrder->valuesSet($data);

        $array = ['salesOrderGoodsAmount' => 0, 'salesOrderTaxAmount' => 0, 'salesOrderAmount' => 0];
        foreach ($goodsData['goodsId'] as $key => $value) {
            $array['salesOrderGoodsAmount']  = $array['salesOrderGoodsAmount'] + $goodsData['salesGoodsPrice'][$key] * $goodsData['salesGoodsSellNum'][$key];
            $array['salesOrderTaxAmount']    = $array['salesOrderTaxAmount'] + $goodsData['salesGoodsTax'][$key];
            $array['salesOrderAmount']        = $array['salesOrderAmount'] + $goodsData['salesGoodsAmount'][$key];
        }

        $salesOrder->setSalesOrderGoodsAmount($array['salesOrderGoodsAmount']);
        $salesOrder->setSalesOrderTaxAmount($array['salesOrderTaxAmount']);
        $salesOrder->setSalesOrderAmount($array['salesOrderAmount']);

        $this->entityManager->flush();
        return true;
    }

    /**
     * 更新销售订单金额
     * @param array $data
     * @param SalesOrder $salesOrder
     */
    public function updateSalesOrderAmount(array $data, SalesOrder $salesOrder)
    {
        $salesOrder->setSalesOrderGoodsAmount($data['salesOrderGoodsAmount']);
        if(isset($data['salesOrderTaxAmount']) && $data['salesOrderTaxAmount'] > 0) $salesOrder->setSalesOrderTaxAmount($data['salesOrderTaxAmount']);
        $salesOrder->setSalesOrderAmount($data['salesOrderAmount']);
        $this->entityManager->flush();
    }

    /**
     * 更新销售订单状态
     * @param $state
     * @param SalesOrder $salesOrder
     */
    public function updateSalesOrderState($state, SalesOrder $salesOrder)
    {
        $salesOrder->setSalesOrderState($state);
        $this->entityManager->flush();
    }

    /**
     * 更新销售订单退货状态
     * @param $state
     * @param SalesOrder $salesOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateSalesOrderReturnState($state, SalesOrder $salesOrder)
    {
        $salesOrder->setReturnState($state);
        $this->entityManager->flush();
    }

    /**
     * 删除订单
     * @param SalesOrder $salesOrder
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteSalesOrder(SalesOrder $salesOrder)
    {
        $this->entityManager->remove($salesOrder);
        $this->entityManager->flush();

        return true;
    }
}