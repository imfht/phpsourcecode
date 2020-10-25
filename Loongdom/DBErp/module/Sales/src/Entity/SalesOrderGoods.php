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
 * 销售商品
 * Class SalesOrderGoods
 * @package Sales\Entity
 * @ORM\Entity(repositoryClass="Sales\Repository\SalesOrderGoodsRepository")
 * @ORM\Table(name="dberp_sales_order_goods")
 */
class SalesOrderGoods
{
    /**
     * 销售商品id
     * @ORM\Id()
     * @ORM\Column(name="sales_goods_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $salesGoodsId;

    /**
     * 销售订单id
     * @ORM\Column(name="sales_order_id", type="integer", length=11)
     */
    private $salesOrderId;

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
     * 销售数量
     * @ORM\Column(name="sales_goods_sell_num", type="integer", length=11)
     */
    private $salesGoodsSellNum;

    /**
     * 商品单价
     * @ORM\Column(name="sales_goods_price", type="decimal", scale=4)
     */
    private $salesGoodsPrice;

    /**
     * 税金
     * @ORM\Column(name="sales_goods_tax", type="decimal", scale=4)
     */
    private $salesGoodsTax;

    /**
     * 商品总价
     * @ORM\Column(name="sales_goods_amount", type="decimal", scale=4)
     */
    private $salesGoodsAmount;

    /**
     * 商品备注
     * @ORM\Column(name="sales_goods_info", type="string", length=255)
     */
    private $salesGoodsInfo;

    /**
     * @return mixed
     */
    public function getSalesGoodsId()
    {
        return $this->salesGoodsId;
    }

    /**
     * @param mixed $salesGoodsId
     */
    public function setSalesGoodsId($salesGoodsId)
    {
        $this->salesGoodsId = $salesGoodsId;
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

    /**
     * @return mixed
     */
    public function getSalesGoodsSellNum()
    {
        return $this->salesGoodsSellNum;
    }

    /**
     * @param mixed $salesGoodsSellNum
     */
    public function setSalesGoodsSellNum($salesGoodsSellNum)
    {
        $this->salesGoodsSellNum = $salesGoodsSellNum;
    }

    /**
     * @return mixed
     */
    public function getSalesGoodsPrice()
    {
        return $this->salesGoodsPrice;
    }

    /**
     * @param mixed $salesGoodsPrice
     */
    public function setSalesGoodsPrice($salesGoodsPrice)
    {
        $this->salesGoodsPrice = $salesGoodsPrice;
    }

    /**
     * @return mixed
     */
    public function getSalesGoodsTax()
    {
        return $this->salesGoodsTax;
    }

    /**
     * @param mixed $salesGoodsTax
     */
    public function setSalesGoodsTax($salesGoodsTax)
    {
        $this->salesGoodsTax = $salesGoodsTax;
    }

    /**
     * @return mixed
     */
    public function getSalesGoodsAmount()
    {
        return $this->salesGoodsAmount;
    }

    /**
     * @param mixed $salesGoodsAmount
     */
    public function setSalesGoodsAmount($salesGoodsAmount)
    {
        $this->salesGoodsAmount = $salesGoodsAmount;
    }

    /**
     * @return mixed
     */
    public function getSalesGoodsInfo()
    {
        return $this->salesGoodsInfo;
    }

    /**
     * @param mixed $salesGoodsInfo
     */
    public function setSalesGoodsInfo($salesGoodsInfo)
    {
        $this->salesGoodsInfo = $salesGoodsInfo;
    }

}