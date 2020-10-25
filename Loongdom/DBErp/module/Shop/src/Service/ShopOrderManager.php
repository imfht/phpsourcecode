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

namespace Shop\Service;

use Admin\Entity\App;
use Doctrine\ORM\EntityManager;
use Shop\Entity\ShopOrder;

class ShopOrderManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加商城订单
     * @param array $data
     * @param int $appId
     * @return ShopOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addShopOrder(array $data, int $appId)
    {
        $appInfo = $this->entityManager->getRepository(App::class)->findOneByAppId($appId);

        $shopOrder = new ShopOrder();
        $shopOrder->setShopOrderId(null);
        $shopOrder->setAppId($appId);
        $shopOrder->setShopOrderSn($data['order_sn']);
        $shopOrder->setShopBuyName($data['buy_name']);
        $shopOrder->setShopPaymentCode($data['payment_code']);
        $shopOrder->setShopPaymentName($data['payment_name']);
        $shopOrder->setShopPaymentCost($data['payment_cost']);
        $shopOrder->setShopPaymentCertification($data['payment_certification']);
        $shopOrder->setShopExpressCode($data['express_code']);
        $shopOrder->setShopExpressName($data['express_name']);
        $shopOrder->setShopExpressCost($data['express_cost']);
        $shopOrder->setShopOrderOtherCost($data['other_cost']);
        $shopOrder->setShopOrderOtherInfo($data['other_info']);
        $shopOrder->setShopOrderDiscountAmount($data['discount_amount']);
        $shopOrder->setShopOrderDiscountInfo($data['discount_info']);
        $shopOrder->setShopOrderGoodsAmount($data['goods_amount']);
        $shopOrder->setShopOrderAmount($data['order_amount']);
        $shopOrder->setShopOrderMessage($data['order_message']);
        $shopOrder->setShopOrderAddTime($data['add_time']);
        $shopOrder->setOneApp($appInfo);
        //货到付款设置为 30 待发货，其他为10 待付款
        $shopOrder->setShopOrderState(($data['payment_code'] == 'hdfk' ? 30 : 10));

        $this->entityManager->persist($shopOrder);
        $this->entityManager->flush();

        return $shopOrder;
    }

    /**
     * 只有在订单取消的情况下才会删除订单
     * @param ShopOrder $shopOrder
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteShopOrder(ShopOrder $shopOrder)
    {
        $this->entityManager->remove($shopOrder);
        $this->entityManager->flush();

        return true;
    }

    /**
     * 付款完成
     * @param array $data
     * @param $state
     * @param ShopOrder $shopOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function paymentFinishShopOrder(array $data, $state, ShopOrder $shopOrder)
    {
        $shopOrder->setShopOrderState($state);
        $shopOrder->setShopOrderPayTime($data['oper_time']);
        $this->entityManager->flush();
    }

    /**
     * 发货完成
     * @param array $data
     * @param $state
     * @param ShopOrder $shopOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deliverFinishShopOrder(array $data, $state, ShopOrder $shopOrder)
    {
        $shopOrder->setShopOrderState($state);
        $shopOrder->setShopOrderExpressTime($data['oper_time']);
        $this->entityManager->flush();
    }

    /**
     * 订单完成
     * @param array $data
     * @param $state
     * @param ShopOrder $shopOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function finishShopOrder(array $data, $state, ShopOrder $shopOrder)
    {
        $shopOrder->setShopOrderState($state);
        $shopOrder->setShopOrderFinishTime($data['oper_time']);
        $this->entityManager->flush();
    }

    /**
     * 改变订单状态
     * @param $state
     * @param ShopOrder $shopOrder
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeShopOrderState($state, ShopOrder $shopOrder)
    {
        $shopOrder->setShopOrderState($state);
        $this->entityManager->flush();

        return true;
    }
}