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
 * 采购订单商品
 * @package Purchase\Entity
 * @ORM\Entity(repositoryClass="Purchase\Repository\OrderGoodsRepository")
 * @ORM\Table(name="dberp_purchase_order_goods")
 */
class OrderGoods
{
    /**
     * 采购的商品自增id
     * @ORM\Id()
     * @ORM\Column(name="p_goods_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $pGoodsId;

    /**
     * 采购订单id
     * @ORM\Column(name="p_order_id", type="integer", length=11)
     */
    private $pOrderId;

    /**
     * 商品id
     * @ORM\Column(name="goods_id", type="integer", length=11)
     */
    private $goodsId;

    /**
     * 采购商品名称
     * @ORM\Column(name="goods_name", type="string", length=100)
     */
    private $goodsName;

    /**
     * 采购商品编号
     * @ORM\Column(name="goods_number", type="string", length=30)
     */
    private $goodsNumber;

    /**
     * 采购商品规格
     * @ORM\Column(name="goods_spec", type="string", length=100)
     */
    private $goodsSpec;

    /**
     * 采购商品单位
     * @ORM\Column(name="goods_unit", type="string", length=20)
     */
    private $goodsUnit;

    /**
     * 采购商品数量
     * @ORM\Column(name="p_goods_buy_num", type="integer", length=11)
     */
    private $pGoodsBuyNum;

    /**
     * 采购商品金额
     * @ORM\Column(name="p_goods_price", type="decimal", scale=4)
     */
    private $pGoodsPrice;

    /**
     * 采购商品税金
     * @ORM\Column(name="p_goods_tax", type="decimal", scale=4)
     */
    private $pGoodsTax;

    /**
     * 采购商品总金额
     * @ORM\Column(name="p_goods_amount", type="decimal", scale=4)
     */
    private $pGoodsAmount;

    /**
     * 采购单商品备注
     * @ORM\Column(name="p_goods_info", type="string", length=255)
     */
    private $pGoodsInfo;

    /**
     * @return mixed
     */
    public function getPGoodsId()
    {
        return $this->pGoodsId;
    }

    /**
     * @param mixed $pGoodsId
     */
    public function setPGoodsId($pGoodsId)
    {
        $this->pGoodsId = $pGoodsId;
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

    /**
     * @return mixed
     */
    public function getPGoodsBuyNum()
    {
        return $this->pGoodsBuyNum;
    }

    /**
     * @param mixed $pGoodsBuyNum
     */
    public function setPGoodsBuyNum($pGoodsBuyNum)
    {
        $this->pGoodsBuyNum = $pGoodsBuyNum;
    }

    /**
     * @return mixed
     */
    public function getPGoodsPrice()
    {
        return $this->pGoodsPrice;
    }

    /**
     * @param mixed $pGoodsPrice
     */
    public function setPGoodsPrice($pGoodsPrice)
    {
        $this->pGoodsPrice = $pGoodsPrice;
    }

    /**
     * @return mixed
     */
    public function getPGoodsTax()
    {
        return $this->pGoodsTax;
    }

    /**
     * @param mixed $pGoodsTax
     */
    public function setPGoodsTax($pGoodsTax)
    {
        $this->pGoodsTax = $pGoodsTax;
    }

    /**
     * @return mixed
     */
    public function getPGoodsAmount()
    {
        return $this->pGoodsAmount;
    }

    /**
     * @param mixed $pGoodsAmount
     */
    public function setPGoodsAmount($pGoodsAmount)
    {
        $this->pGoodsAmount = $pGoodsAmount;
    }

    /**
     * @return mixed
     */
    public function getPGoodsInfo()
    {
        return $this->pGoodsInfo;
    }

    /**
     * @param mixed $pGoodsInfo
     */
    public function setPGoodsInfo($pGoodsInfo)
    {
        $this->pGoodsInfo = $pGoodsInfo;
    }

}