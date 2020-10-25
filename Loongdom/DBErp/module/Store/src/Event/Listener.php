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

namespace Store\Event;

use Doctrine\ORM\EntityManager;
use Purchase\Entity\WarehouseOrderGoods;
use Stock\Entity\OtherWarehouseOrderGoods;
use Stock\Entity\StockCheckGoods;
use Store\Entity\Goods;
use Store\Entity\WarehouseGoods;
use Store\Service\GoodsManager;
use Store\Service\WarehouseGoodsManager;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

class Listener implements ListenerAggregateInterface
{
    protected $listeners = [];

    private $entityManager;
    private $goodsManager;
    private $warehouseGoodsManager;

    public function __construct(
        EntityManager   $entityManager,
        GoodsManager    $goodsManager,
        WarehouseGoodsManager $warehouseGoodsManager
    )
    {
        $this->entityManager    = $entityManager;
        $this->goodsManager     = $goodsManager;
        $this->warehouseGoodsManager = $warehouseGoodsManager;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $shareEvents = $events->getSharedManager();

        //商品价格与库存更新，验收采购入库，直接入库
        $this->listeners[] = $shareEvents->attach(
            'Purchase\Controller\WarehouseOrderController', 'warehouse-order.add.post', [$this, 'onUpdateGoodsPriceAndStockAndWarehouseGoods']
        );

        //商品价格与库存更新，待入库单入库
        $this->listeners[] = $shareEvents->attach(
            'Purchase\Controller\WarehouseOrderController', 'warehouse-order.insert.post', [$this, 'onUpdateGoodsPriceAndStockAndWarehouseGoods']
        );

        //库存-其他入库，入库处理
        $this->listeners[] = $shareEvents->attach(
            'Stock\Controller\IndexController', 'other-warehouse-order.insert.post', [$this, 'onOtherUpdateGoodsPriceAndStockAndWarehouseGoods']
        );

        //库存-盘点，确认处理
        $this->listeners[] = $shareEvents->attach(
            'Stock\Controller\StockCheckController', 'stock-check.update.post', [$this, 'onStockCheckUpdateGoodsStockAndWarehouseGoods']
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
     * 更新商品的价格、商品库存、仓库库存(采购入库)
     * @param Event $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onUpdateGoodsPriceAndStockAndWarehouseGoods(Event $event)
    {
        $warehouseOrder = $event->getParams();

        if($warehouseOrder->getWarehouseOrderState() == 3) {//只有当入库时，才会进行处理
            $orderGoods = $this->entityManager->getRepository(WarehouseOrderGoods::class)->findBy(['warehouseOrderId' => $warehouseOrder->getWarehouseOrderId()]);
            if($orderGoods != null) {
                foreach ($orderGoods as $goodsObject) {
                    if($goodsObject->getWarehouseGoodsBuyNum() <= 0) continue;

                    $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $goodsObject->getGoodsId()]);
                    if($goodsInfo) {
                        $data = [
                            'goodsStock' => $goodsInfo->getGoodsStock() + $goodsObject->getWarehouseGoodsBuyNum(),
                            'goodsPrice' => $goodsObject->getWarehouseGoodsPrice()
                        ];
                        //先在仓库中写入
                        $warehouseGoods = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $warehouseOrder->getWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId()]);
                        if($warehouseGoods == null) {
                            $this->warehouseGoodsManager->addWarehouseGoods(['warehouseId' => $warehouseOrder->getWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId(), 'warehouseGoodsStock' => $goodsObject->getWarehouseGoodsBuyNum()]);
                        } else $this->warehouseGoodsManager->updateWarehouseGoodsStock($warehouseGoods->getWarehouseGoodsStock()+$goodsObject->getWarehouseGoodsBuyNum(), $warehouseGoods);
                        //商品中更新
                        $this->goodsManager->updateGoodsPriceAndStock($data, $goodsInfo);
                    }
                }
            }

        }
    }

    /**
     * 更新商品的价格、商品库存、仓库库存(其他入库)
     * @param Event $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onOtherUpdateGoodsPriceAndStockAndWarehouseGoods(Event $event)
    {
        $otherWarehouseOrder = $event->getParams();
        $otherOrderGoods = $this->entityManager->getRepository(OtherWarehouseOrderGoods::class)->findBy(['otherWarehouseOrderId' => $otherWarehouseOrder->getOtherWarehouseOrderId()]);
        if($otherOrderGoods != null) {
            foreach ($otherOrderGoods as $goodsObject) {
                if($goodsObject->getWarehouseGoodsBuyNum() <= 0) continue;

                $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $goodsObject->getGoodsId()]);
                if($goodsInfo) {
                    $data = [
                        'goodsStock' => $goodsInfo->getGoodsStock() + $goodsObject->getWarehouseGoodsBuyNum(),
                        //'goodsPrice' => $goodsObject->getWarehouseGoodsPrice()
                        'goodsPrice' => $goodsInfo->getGoodsPrice() //在其他入库中，不将价格更新
                    ];
                    $warehouseGoods = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $otherWarehouseOrder->getWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId()]);
                    if($warehouseGoods == null) {
                        $this->warehouseGoodsManager->addWarehouseGoods(['warehouseId' => $otherWarehouseOrder->getWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId(), 'warehouseGoodsStock' => $goodsObject->getWarehouseGoodsBuyNum()]);
                    } else $this->warehouseGoodsManager->updateWarehouseGoodsStock($warehouseGoods->getWarehouseGoodsStock() + $goodsObject->getWarehouseGoodsBuyNum(), $warehouseGoods);

                    //商品中更新
                    $this->goodsManager->updateGoodsPriceAndStock($data, $goodsInfo);
                }
            }
        }
    }

    /**
     * 更新库存（库存盘点）
     * @param Event $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onStockCheckUpdateGoodsStockAndWarehouseGoods(Event $event)
    {
        $stockCheckInfo = $event->getParams();
        $stockCheckGoods= $this->entityManager->getRepository(StockCheckGoods::class)->findBy(['stockCheckId' => $stockCheckInfo->getStockCheckId()]);
        foreach ($stockCheckGoods as $stockGoods) {
            if($stockGoods->getStockCheckAftGoodsNum() <= 0) continue;

            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $stockGoods->getGoodsId()]);
            if($goodsInfo) {
                $addGoodsStock  = $stockGoods->getStockCheckAftGoodsNum() - $stockGoods->getStockCheckPreGoodsNum();
                $goodsStock     = $goodsInfo->getGoodsStock() + $addGoodsStock;

                $warehouseGoods = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $stockCheckInfo->getWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId()]);
                if($warehouseGoods == null) {
                    $this->warehouseGoodsManager->addWarehouseGoods(['warehouseId' => $stockCheckInfo->getWarehouseId(), 'goodsId' => $goodsInfo->getGoodsId(), 'warehouseGoodsStock' => $addGoodsStock]);
                } else $this->warehouseGoodsManager->updateWarehouseGoodsStock($warehouseGoods->getWarehouseGoodsStock() + $addGoodsStock, $warehouseGoods);

                $this->goodsManager->updateGoodsStock($goodsStock, $goodsInfo);
            }
        }
    }
}