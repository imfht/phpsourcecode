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

namespace Purchase\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 入库订单商品表
 * @package Purchase\Entity
 * @ORM\Entity(repositoryClass="Purchase\Repository\WarehouseOrderGoodsRepository")
 * @ORM\Table(name="dberp_purchase_warehouse_order_goods")
 */
class WarehouseOrderGoods
{
    /**
     * @var
     * @ORM\Id()
     * @ORM\Column(name="warehouse_order_goods_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $warehouseOrderGoodsId;

    /**
     * @var
     * @ORM\Column(name="warehouse_order_id", type="integer", length=11)
     */
    private $warehouseOrderId;

    /**
     * @var
     * @ORM\Column(name="warehouse_id", type="integer", length=11)
     */
    private $warehouseId;

    /**
     * @var
     * @ORM\Column(name="warehouse_goods_buy_num", type="integer", length=11)
     */
    private $warehouseGoodsBuyNum;

    /**
     * @var
     * @ORM\Column(name="warehouse_goods_price", type="decimal", scale=4)
     */
    private $warehouseGoodsPrice;

    /**
     * @var
     * @ORM\Column(name="warehouse_goods_tax", type="decimal", scale=4)
     */
    private $warehouseGoodsTax;

    /**
     * @var
     * @ORM\Column(name="warehouse_goods_amount", type="decimal", scale=4)
     */
    private $warehouseGoodsAmount;

    /**
     * @var
     * @ORM\Column(name="p_order_id", type="integer", length=11)
     */
    private $pOrderId;

    /**
     * @var
     * @ORM\Column(name="goods_id", type="integer", length=11)
     */
    private $goodsId;

    /**
     * @var
     * @ORM\Column(name="goods_name", type="string", length=100)
     */
    private $goodsName;

    /**
     * @var
     * @ORM\Column(name="goods_number", type="string", length=30)
     */
    private $goodsNumber;

    /**
     * @var
     * @ORM\Column(name="goods_spec", type="string", length=100)
     */
    private $goodsSpec;

    /**
     * @var
     * @ORM\Column(name="goods_unit", type="string", length=20)
     */
    private $goodsUnit;

    /**
     * @return mixed
     */
    public function getWarehouseOrderGoodsId()
    {
        return $this->warehouseOrderGoodsId;
    }

    /**
     * @param mixed $warehouseOrderGoodsId
     */
    public function setWarehouseOrderGoodsId($warehouseOrderGoodsId)
    {
        $this->warehouseOrderGoodsId = $warehouseOrderGoodsId;
    }

    /**
     * @return mixed
     */
    public function getWarehouseOrderId()
    {
        return $this->warehouseOrderId;
    }

    /**
     * @param mixed $warehouseOrderId
     */
    public function setWarehouseOrderId($warehouseOrderId)
    {
        $this->warehouseOrderId = $warehouseOrderId;
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
    public function getWarehouseGoodsBuyNum()
    {
        return $this->warehouseGoodsBuyNum;
    }

    /**
     * @param mixed $warehouseGoodsBuyNum
     */
    public function setWarehouseGoodsBuyNum($warehouseGoodsBuyNum)
    {
        $this->warehouseGoodsBuyNum = $warehouseGoodsBuyNum;
    }

    /**
     * @return mixed
     */
    public function getWarehouseGoodsPrice()
    {
        return $this->warehouseGoodsPrice;
    }

    /**
     * @param mixed $warehouseGoodsPrice
     */
    public function setWarehouseGoodsPrice($warehouseGoodsPrice)
    {
        $this->warehouseGoodsPrice = $warehouseGoodsPrice;
    }

    /**
     * @return mixed
     */
    public function getWarehouseGoodsTax()
    {
        return $this->warehouseGoodsTax;
    }

    /**
     * @param mixed $warehouseGoodsTax
     */
    public function setWarehouseGoodsTax($warehouseGoodsTax)
    {
        $this->warehouseGoodsTax = $warehouseGoodsTax;
    }

    /**
     * @return mixed
     */
    public function getWarehouseGoodsAmount()
    {
        return $this->warehouseGoodsAmount;
    }

    /**
     * @param mixed $warehouseGoodsAmount
     */
    public function setWarehouseGoodsAmount($warehouseGoodsAmount)
    {
        $this->warehouseGoodsAmount = $warehouseGoodsAmount;
    }

    /**
     * @return mixed
     */
    public function getPOrderId()
    {
        return $this->pOrderId;
    }

    /**
     * @param mixed $pOrderId
     */
    public function setPOrderId($pOrderId)
    {
        $this->pOrderId = $pOrderId;
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
    public function getGoodsNumber()
    {
        return $this->goodsNumber;
    }

    /**
     * @param mixed $goodsNumber
     */
    public function setGoodsNumber($goodsNumber)
    {
        $this->goodsNumber = $goodsNumber;
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
    public function getGoodsUnit()
    {
        return $this->goodsUnit;
    }

    /**
     * @param mixed $goodsUnit
     */
    public function setGoodsUnit($goodsUnit)
    {
        $this->goodsUnit = $goodsUnit;
    }
}