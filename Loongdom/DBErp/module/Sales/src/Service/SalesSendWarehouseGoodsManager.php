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
use Sales\Entity\SalesOrder;
use Sales\Entity\SalesOrderGoods;
use Sales\Entity\SalesSendOrder;
use Sales\Entity\SalesSendWarehouseGoods;
use Store\Entity\Warehouse;
use Store\Entity\WarehouseGoods;

class SalesSendWarehouseGoodsManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加仓库发货商品
     * @param array $data
     * @param SalesSendOrder $sendOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addSalesSendWarehouseGoods(array $data, SalesSendOrder $sendOrder)
    {

        foreach ($data as $value) {
            $oneWarehouse = $this->entityManager->getRepository(Warehouse::class)->findOneByWarehouseId($value['warehouseId']);

            $sendWarehouseGoods = new SalesSendWarehouseGoods();
            $sendWarehouseGoods->setSendWarehouseGoodsId(null);
            $sendWarehouseGoods->setSalesOrderId($sendOrder->getSalesOrderId());
            $sendWarehouseGoods->setSendOrderId($sendOrder->getSendOrderId());
            $sendWarehouseGoods->setGoodsId($value['goodsId']);
            $sendWarehouseGoods->setWarehouseId($value['warehouseId']);
            $sendWarehouseGoods->setSendGoodsStock($value['sendNum']);
            $sendWarehouseGoods->setOneWarehouse($oneWarehouse);

            $this->entityManager->persist($sendWarehouseGoods);
            $this->entityManager->flush();
        }
    }

    /**
     * 检查并获取仓库需要的出库商品
     * @param array $data
     * @param $salesOrderGoods
     * @return array
     */
    public function checkAndReturnSendWarehouseGoodsNum(array $data, $salesOrderGoods)
    {
        $sendWarehouseGoods = [];
        $goodsStock = [];
        foreach ($salesOrderGoods as $goodsValue) {
            if(count($data['sendWarehouse'][$goodsValue->getGoodsId()]) > 1) rsort($data['sendWarehouse'][$goodsValue->getGoodsId()]);
            $goodsStock[$goodsValue->getGoodsId()] = $goodsValue->getSalesGoodsSellNum();//获取商品需要减少的库存
            $salesGoodsNum= $goodsValue->getSalesGoodsSellNum();
            foreach ($data['sendWarehouse'][$goodsValue->getGoodsId()] as $warehouseId) {
                $warehouseGoods = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $warehouseId, 'goodsId' => $goodsValue->getGoodsId()]);
                //在第一个仓库如果已经满足发货要求，则直接在第一个仓库出货
                if($warehouseGoods->getWarehouseGoodsStock() >= $salesGoodsNum) {
                    $sendWarehouseGoods[] = ['warehouseId' => $warehouseId, 'goodsId' => $goodsValue->getGoodsId(), 'sendNum' => $salesGoodsNum];
                    break;
                } else {
                    $sendWarehouseGoods[] = ['warehouseId' => $warehouseId, 'goodsId' => $goodsValue->getGoodsId(), 'sendNum' => $warehouseGoods->getWarehouseGoodsStock()];
                    $salesGoodsNum = $salesGoodsNum - $warehouseGoods->getWarehouseGoodsStock();
                }
            }
        }
        return ['goods' => $goodsStock, 'warehouseGoods' => $sendWarehouseGoods];
    }
}