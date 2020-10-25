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

namespace Shop\Event;

use Doctrine\ORM\EntityManager;
use Shop\Entity\ShopOrderGoods;
use Shop\Service\ShopOrderGoodsManager;
use Store\Entity\Goods;
use Store\Entity\WarehouseGoods;
use Store\Service\GoodsManager;
use Store\Service\WarehouseGoodsManager;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Session\Container;

class Listener implements ListenerAggregateInterface
{
    protected $listeners = [];

    private $entityManager;
    private $shopOrderGoodsManager;
    private $warehouseGoodsManager;
    private $goodsManager;

    public function __construct(
        EntityManager   $entityManager,
        ShopOrderGoodsManager $shopOrderGoodsManager,
        WarehouseGoodsManager $warehouseGoodsManager,
        GoodsManager    $goodsManager
    )
    {
        $this->entityManager    = $entityManager;
        $this->shopOrderGoodsManager = $shopOrderGoodsManager;
        $this->warehouseGoodsManager = $warehouseGoodsManager;
        $this->goodsManager     = $goodsManager;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $shareEvents = $events->getSharedManager();

        //订单发货，对匹配的商品库存进行调整
        $this->listeners[] = $shareEvents->attach(
            'Api\Controller\IndexController', 'app-shop.deliver.post', [$this, 'onUpdateWarehouseGoodsStock']
        );
        //订单完成，对匹配的商品库存进行调整
        $this->listeners[] = $shareEvents->attach(
            'Api\Controller\IndexController', 'app-shop.finish.post', [$this, 'onUpdateShopOrderGoodsState']
        );
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            $events->detach($listener);
            unset($this->listeners[$index]);
        }
    }

    /**
     * 订单发货，对匹配的商品库存进行调整
     * @param Event $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onUpdateWarehouseGoodsStock(Event $event)
    {
        $shopOrder = $event->getParams();

        $managerId      = $shopOrder->getManagerId();
        $shopOrderGoods = $this->entityManager->getRepository(ShopOrderGoods::class)->findBy(['shopOrderId' => $shopOrder->getShopOrderId()]);
        echo $managerId;
        if($shopOrderGoods) {
            foreach ($shopOrderGoods as $orderGoodsValue) {
                $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsNumber' => $orderGoodsValue->getGoodsSn()]);
                if($goodsInfo) {
                    $warehouse = $this->entityManager->getRepository(WarehouseGoods::class)->findWarehouseStockGoods($goodsInfo->getGoodsId(), $orderGoodsValue->getBuyNum());
                    if($warehouse) {//
                        foreach ($warehouse as $warehouseGoods) {
                            $this->warehouseGoodsManager->updateWarehouseGoodsStock(($warehouseGoods->getWarehouseGoodsStock() - $orderGoodsValue->getBuyNum()), $warehouseGoods);
                            $this->goodsManager->updateGoodsStock(($goodsInfo->getGoodsStock() - $orderGoodsValue->getBuyNum()), $goodsInfo);
                            $this->shopOrderGoodsManager->addShopOrderGoodsWarehouseAndState($warehouseGoods->getWarehouseId(), $warehouseGoods->getOneWarehouse()->getWarehouseName(), 6, $orderGoodsValue);
                            break;
                        }
                    } else {//缺货处理
                        $this->shopOrderGoodsManager->updateShopOrderGoodsState(-1, $orderGoodsValue);
                    }
                } else {//不匹配处理
                    $this->shopOrderGoodsManager->updateShopOrderGoodsState(3, $orderGoodsValue);
                }

            }
        }
    }

    /**
     * 订单完成，对匹配的商品库存进行调整
     * @param Event $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onUpdateShopOrderGoodsState(Event $event)
    {
        $shopOrder = $event->getParams();

        $managerId      = $shopOrder->getManagerId();
        $shopOrderGoods = $this->entityManager->getRepository(ShopOrderGoods::class)->findBy(['shopOrderId' => $shopOrder->getShopOrderId()]);
        if($shopOrderGoods) {
            foreach ($shopOrderGoods as $orderGoodsValue) {
                if($orderGoodsValue->getDistributionState() == 6) $this->shopOrderGoodsManager->updateShopOrderGoodsState(12, $orderGoodsValue);
            }
        }
    }
}