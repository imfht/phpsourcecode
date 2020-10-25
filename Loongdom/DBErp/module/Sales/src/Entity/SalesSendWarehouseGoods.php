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

namespace Sales\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 订单发货仓库商品出库表
 * @package Sales\Entity
 * @ORM\Entity(repositoryClass="Sales\Repository\SalesSendWarehouseGoodsRepository")
 * @ORM\Table(name="dberp_sales_send_warehouse_goods")
 */
class SalesSendWarehouseGoods
{
    /**
     * id
     * @ORM\Id()
     * @ORM\Column(name="send_warehouse_goods_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sendWarehouseGoodsId;

    /**
     * 商品id
     * @ORM\Column(name="goods_id", type="integer", length=11)
     */
    private $goodsId;

    /**
     * 仓库id
     * @ORM\Column(name="warehouse_id", type="integer", length=11)
     */
    private $warehouseId;

    /**
     * 出库数量
     * @ORM\Column(name="send_goods_stock", type="integer", length=11)
     */
    private $sendGoodsStock;

    /**
     * 订单发送id
     * @ORM\Column(name="send_order_id", type="integer", length=11)
     */
    private $sendOrderId;

    /**
     * 销售订单id
     * @ORM\Column(name="sales_order_id", type="integer", length=11)
     */
    private $salesOrderId;

    /**
     * 一对一，仓库信息
     * @ORM\OneToOne(targetEntity="Store\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="warehouse_id")
     */
    private $oneWarehouse;

    /**
     * @return mixed
     */
    public function getOneWarehouse()
    {
        return $this->oneWarehouse;
    }

    /**
     * @param mixed $oneWarehouse
     */
    public function setOneWarehouse($oneWarehouse)
    {
        $this->oneWarehouse = $oneWarehouse;
    }

    /**
     * @return mixed
     */
    public function getSendWarehouseGoodsId()
    {
        return $this->sendWarehouseGoodsId;
    }

    /**
     * @param mixed $sendWarehouseGoodsId
     */
    public function setSendWarehouseGoodsId($sendWarehouseGoodsId)
    {
        $this->sendWarehouseGoodsId = $sendWarehouseGoodsId;
    }

    /**
     * @return mixed
     */
    public function getGoodsId()
    {
        return $this->goodsId;
    }

    /**
     * @param mixed $goodsId
     */
    public function setGoodsId($goodsId)
    {
        $this->goodsId = $goodsId;
    }

    /**
     * @return mixed
     */
    public function getWarehouseId()
    {
        return $this->warehouseId;
    }

    /**
     * @param mixed $warehouseId
     */
    public function setWarehouseId($warehouseId)
    {
        $this->warehouseId = $warehouseId;
    }

    /**
     * @return mixed
     */
    public function getSendGoodsStock()
    {
        return $this->sendGoodsStock;
    }

    /**
     * @param mixed $sendGoodsStock
     */
    public function setSendGoodsStock($sendGoodsStock)
    {
        $this->sendGoodsStock = $sendGoodsStock;
    }

    /**
     * @return mixed
     */
    public function getSendOrderId()
    {
        return $this->sendOrderId;
    }

    /**
     * @param mixed $sendOrderId
     */
    public function setSendOrderId($sendOrderId)
    {
        $this->sendOrderId = $sendOrderId;
    }

    /**
     * @return mixed
     */
    public function getSalesOrderId()
    {
        return $this->salesOrderId;
    }

    /**
     * @param mixed $salesOrderId
     */
    public function setSalesOrderId($salesOrderId)
    {
        $this->salesOrderId = $salesOrderId;
    }

}