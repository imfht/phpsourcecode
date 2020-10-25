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

namespace Stock\Entity;

use Admin\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class OtherWarehouseOrderGoods
 * @package Stock\Entity
 * @ORM\Entity(repositoryClass="Stock\Repository\OtherWarehouseOrderGoodsRepository")
 * @ORM\Table(name="dberp_other_warehouse_order_goods")
 */
class OtherWarehouseOrderGoods extends BaseEntity
{
    /**
     * 入库商品id
     * @ORM\Id()
     * @ORM\Column(name="warehouse_order_goods_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $warehouseOrderGoodsId;

    /**
     * 入库对应表id
     * @ORM\Column(name="other_warehouse_order_id", type="integer", length=11)
     */
    private $otherWarehouseOrderId;

    /**
     * 入库id
     * @ORM\Column(name="warehouse_id", type="integer", length=11)
     */
    private $warehouseId;

    /**
     * 购买数量
     * @ORM\Column(name="warehouse_goods_buy_num", type="integer", length=11)
     */
    private $warehouseGoodsBuyNum;

    /**
     * 商品价格
     * @ORM\Column(name="warehouse_goods_price", type="decimal", scale=4)
     */
    private $warehouseGoodsPrice;

    /**
     * 商品税金
     * @ORM\Column(name="warehouse_goods_tax", type="decimal", scale=4)
     */
    private $warehouseGoodsTax;

    /**
     * 商品总金额
     * @ORM\Column(name="warehouse_goods_amount", type="decimal", scale=4)
     */
    private $warehouseGoodsAmount;

    /**
     * 商品id
     * @ORM\Column(name="goods_id", type="integer", length=11)
     */
    private $goodsId;

    /**
     * 商品名称
     * @ORM\Column(name="goods_name", type="string", length=100)
     */
    private $goodsName;

    /**
     * 商品编号
     * @ORM\Column(name="goods_number", type="string", length=30)
     */
    private $goodsNumber;

    /**
     * 商品规格
     * @ORM\Column(name="goods_spec", type="string", length=100)
     */
    private $goodsSpec;

    /**
     * 商品单位
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
    public function getOtherWarehouseOrderId()
    {
        return $this->otherWarehouseOrderId;
    }

    /**
     * @param mixed $otherWarehouseOrderId
     */
    public function setOtherWarehouseOrderId($otherWarehouseOrderId)
    {
        $this->otherWarehouseOrderId = $otherWarehouseOrderId;
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