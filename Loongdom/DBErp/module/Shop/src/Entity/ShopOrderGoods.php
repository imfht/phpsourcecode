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

namespace Shop\Entity;

use Admin\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * 商城订单商品
 * Class ShopOrderGoods
 * @package Shop\Entity
 * @ORM\Entity(repositoryClass="Shop\Repository\ShopOrderGoodsRepository")
 * @ORM\Table(name="dberp_shop_order_goods")
 */
class ShopOrderGoods
{
    /**
     * 订单商品自增id
     * @ORM\Id()
     * @ORM\Column(name="order_goods_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $orderGoodsId;

    /**
     * 订单id
     * @ORM\Column(name="shop_order_id", type="integer", length=11)
     */
    private $shopOrderId;

    /**
     * 发货的仓库id
     * @ORM\Column(name="warehouse_id", type="integer", length=11)
     */
    private $warehouseId;

    /**
     * 发货仓库名称
     * @ORM\Column(name="warehouse_name", type="string", length=100)
     */
    private $warehouseName;

    /**
     * 配货状态，-1 缺货，3 未匹配，4 匹配，6 已发货，12 已确认收货
     * @ORM\Column(name="distribution_state", type="integer", length=1)
     */
    private $distributionState;

    /**
     * 商品名称
     * @ORM\Column(name="goods_name", type="string", length=100)
     */
    private $goodsName;

    /**
     * 商品规格
     * @ORM\Column(name="goods_spec", type="string", length=100)
     */
    private $goodsSpec;

    /**
     * 商品编号
     * @ORM\Column(name="goods_sn", type="string", length=30)
     */
    private $goodsSn;

    /**
     * 商品条形码
     * @ORM\Column(name="goods_barcode", type="string", length=30)
     */
    private $goodsBarcode;

    /**
     * 商品单位名称
     * @ORM\Column(name="goods_unit_name", type="string", length=20)
     */
    private $goodsUnitName;

    /**
     * 商品单价
     * @ORM\Column(name="goods_price", type="decimal", scale=4)
     */
    private $goodsPrice;

    /**
     * 商品类型 1 实物，2 虚拟
     * @ORM\Column(name="goods_type", type="integer", length=1)
     */
    private $goodsType;

    /**
     * 商品购买数量
     * @ORM\Column(name="buy_num", type="integer", length=11)
     */
    private $buyNum;

    /**
     * 商品金额
     * @ORM\Column(name="goods_amount", type="decimal", scale=4)
     */
    private $goodsAmount;

    /**
     *
     * @ORM\OneToOne(targetEntity="Shop\Entity\ShopOrder")
     * @ORM\JoinColumn(name="shop_order_id", referencedColumnName="shop_order_id")
     */
    private $oneShopOrder;

    /**
     * @return mixed
     */
    public function getOneShopOrder()
    {
        return $this->oneShopOrder;
    }

    /**
     * @param mixed $oneShopOrder
     */
    public function setOneShopOrder($oneShopOrder)
    {
        $this->oneShopOrder = $oneShopOrder;
    }

    /**
     * @return mixed
     */
    public function getOrderGoodsId()
    {
        return $this->orderGoodsId;
    }

    /**
     * @param mixed $orderGoodsId
     */
    public function setOrderGoodsId($orderGoodsId)
    {
        $this->orderGoodsId = $orderGoodsId;
    }

    /**
     * @return mixed
     */
    public function getShopOrderId()
    {
        return $this->shopOrderId;
    }

    /**
     * @param mixed $shopOrderId
     */
    public function setShopOrderId($shopOrderId)
    {
        $this->shopOrderId = $shopOrderId;
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
    public function getWarehouseName()
    {
        return $this->warehouseName;
    }

    /**
     * @param mixed $warehouseName
     */
    public function setWarehouseName($warehouseName)
    {
        $this->warehouseName = $warehouseName;
    }

    /**
     * @return mixed
     */
    public function getDistributionState()
    {
        return $this->distributionState;
    }

    /**
     * @param mixed $distributionState
     */
    public function setDistributionState($distributionState)
    {
        $this->distributionState = $distributionState;
    }

    /**
     * @return mixed
     */
    public function getGoodsName()
    {
        return $this->goodsName;
    }

    /**
     * @param mixed $goodsName
     */
    public function setGoodsName($goodsName)
    {
        $this->goodsName = $goodsName;
    }

    /**
     * @return mixed
     */
    public function getGoodsSpec()
    {
        return $this->goodsSpec;
    }

    /**
     * @param mixed $goodsSpec
     */
    public function setGoodsSpec($goodsSpec)
    {
        $this->goodsSpec = $goodsSpec;
    }

    /**
     * @return mixed
     */
    public function getGoodsSn()
    {
        return $this->goodsSn;
    }

    /**
     * @param mixed $goodsSn
     */
    public function setGoodsSn($goodsSn)
    {
        $this->goodsSn = $goodsSn;
    }

    /**
     * @return mixed
     */
    public function getGoodsBarcode()
    {
        return $this->goodsBarcode;
    }

    /**
     * @param mixed $goodsBarcode
     */
    public function setGoodsBarcode($goodsBarcode)
    {
        $this->goodsBarcode = $goodsBarcode;
    }

    /**
     * @return mixed
     */
    public function getGoodsUnitName()
    {
        return $this->goodsUnitName;
    }

    /**
     * @param mixed $goodsUnitName
     */
    public function setGoodsUnitName($goodsUnitName)
    {
        $this->goodsUnitName = $goodsUnitName;
    }

    /**
     * @return mixed
     */
    public function getGoodsPrice()
    {
        return $this->goodsPrice;
    }

    /**
     * @param mixed $goodsPrice
     */
    public function setGoodsPrice($goodsPrice)
    {
        $this->goodsPrice = $goodsPrice;
    }

    /**
     * @return mixed
     */
    public function getGoodsType()
    {
        return $this->goodsType;
    }

    /**
     * @param mixed $goodsType
     */
    public function setGoodsType($goodsType)
    {
        $this->goodsType = $goodsType;
    }

    /**
     * @return mixed
     */
    public function getBuyNum()
    {
        return $this->buyNum;
    }

    /**
     * @param mixed $buyNum
     */
    public function setBuyNum($buyNum)
    {
        $this->buyNum = $buyNum;
    }

    /**
     * @return mixed
     */
    public function getGoodsAmount()
    {
        return $this->goodsAmount;
    }

    /**
     * @param mixed $goodsAmount
     */
    public function setGoodsAmount($goodsAmount)
    {
        $this->goodsAmount = $goodsAmount;
    }

}