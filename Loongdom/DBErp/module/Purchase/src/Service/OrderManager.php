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

use Customer\Entity\Supplier;
use Doctrine\ORM\EntityManager;
use Purchase\Entity\Order;

class OrderManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加采购订单
     * @param array $data
     * @param array $goodsData
     * @param int $adminId
     * @return Order
     */
    public function addOrder(array $data, array $goodsData, int $adminId)
    {
        $supplier = $this->entityManager->getRepository(Supplier::class)->findOneBySupplierId($data['supplierId']);

        $order = new Order();
        $order->valuesSet($data);
        $order->setPOrderId(null);

        $order->setPOrderState(0);
        $order->setReturnState(0);
        $order->setAdminId($adminId);

        $order->setOneSupplier($supplier);

        $array = ['pOrderGoodsAmount' => 0, 'pOrderTaxAmount' => 0, 'pOrderAmount' => 0];
        foreach ($goodsData['goodsId'] as $key => $value) {
            $array['pOrderGoodsAmount'] = $array['pOrderGoodsAmount'] + $goodsData['goodsPrice'][$key] * $goodsData['goodsBuyNum'][$key];
            $array['pOrderAmount']      = $array['pOrderAmount'] + $goodsData['goodsAmount'][$key];
            $array['pOrderTaxAmount']   = $array['pOrderTaxAmount'] + $goodsData['goodsTax'][$key];
        }
        $order->setPOrderGoodsAmount(floatval($array['pOrderGoodsAmount']));
        $order->setPOrderTaxAmount(floatval($array['pOrderTaxAmount']));
        $order->setPOrderAmount(floatval($array['pOrderAmount']));

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    /**
     * 编辑更新采购订单
     * @param array $data
     * @param array $goodsData
     * @param Order $order
     * @return bool
     */
    public function updateOrder(array $data, array $goodsData, Order $order)
    {
        $order->valuesSet($data);

        $array = ['pOrderGoodsAmount' => 0, 'pOrderTaxAmount' => 0, 'pOrderAmount' => 0];
        foreach ($goodsData['goodsId'] as $key => $value) {
            $array['pOrderGoodsAmount'] = $array['pOrderGoodsAmount'] + $goodsData['goodsPrice'][$key] * $goodsData['goodsBuyNum'][$key];
            $array['pOrderAmount']      = $array['pOrderAmount'] + $goodsData['goodsAmount'][$key];
            $array['pOrderTaxAmount']   = $array['pOrderTaxAmount'] + $goodsData['goodsTax'][$key];
        }
        $order->setPOrderGoodsAmount(floatval($array['pOrderGoodsAmount']));
        $order->setPOrderTaxAmount(floatval($array['pOrderTaxAmount']));
        $order->setPOrderAmount(floatval($array['pOrderAmount']));

        $this->entityManager->flush();
        return true;
    }

    /**
     * 删除订单
     * @param Order $order
     */
    public function deleteOrder(Order $order)
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();
    }

    /**
     * 更新订单金额信息
     * @param array $data
     * @param Order $order
     */
    public function updateOrderAmount(array $data, Order $order)
    {
        $order->setPOrderGoodsAmount($data['pOrderGoodsAmount']);
        if(isset($data['pOrderTaxAmount']) && $data['pOrderTaxAmount'] > 0) $order->setPOrderTaxAmount($data['pOrderTaxAmount']);
        $order->setPOrderAmount($data['pOrderAmount']);
        $this->entityManager->flush();
    }

    /**
     * 更新采购单状态
     * @param array $data
     * @param Order $order
     */
    public function updateOrderState(array $data, Order $order)
    {
        $order->setPOrderState($data['pOrderState']);
        $this->entityManager->flush();
    }

    /**
     * 更新采购单的退货情况
     * @param $returnState
     * @param Order $order
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateOrderReturnState($returnState, Order $order)
    {
        $order->setReturnState($returnState);
        $this->entityManager->flush();
    }
}