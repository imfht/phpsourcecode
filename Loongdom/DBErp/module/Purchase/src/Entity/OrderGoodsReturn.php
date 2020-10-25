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
 * 退货商品
 * Class OrderGoodsReturn
 * @package Purchase\Entity
 * @ORM\Entity(repositoryClass="Purchase\Repository\OrderGoodsReturnRepository")
 * @ORM\Table(name="dberp_purchase_order_goods_return")
 */
class OrderGoodsReturn
{
    /**
     * 退货商品自增id
     * @ORM\Id()
     * @ORM\Column(name="goods_return_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $goodsReturnId;

    /**
     * 退货单id
     * @ORM\Column(name="order_return_id", type="integer", length=11)
     */
    private $orderReturnId;

    /**
     * 采购商品id
     * @ORM\Column(name="p_goods_id", type="integer", length=11)
     */
    private $pGoodsId;

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
     * 商品单价
     * @ORM\Column(name="p_goods_price", type="decimal", scale=4)
     */
    private $pGoodsPrice;

    /**
     * 商品税金
     * @ORM\Column(name="p_goods_tax", type="decimal", scale=4)
     */
    private $pGoodsTax;

    /**
     * 商品退货数量
     * @ORM\Column(name="goods_return_num", type="integer", length=11)
     */
    private $goodsReturnNum;

    /**
     * 商品退款金额
     * @ORM\Column(name="goods_return_amount", type="decimal", scale=4)
     */
    private $goodsReturnAmount;

    /**
     * @return mixed
     */
    public function getGoodsReturnId()
    {
        return $this->goodsReturnId;
    }

    /**
     * @param mixed $goodsReturnId
     */
    public function setGoodsReturnId($goodsReturnId)
    {
        $this->goodsReturnId = $goodsReturnId;
    }

    /**
     * @return mixed
     */
    public function getOrderReturnId()
    {
        return $this->orderReturnId;
    }

    /**
     * @param mixed $orderReturnId
     */
    public function setOrderReturnId($orderReturnId)
    {
        $this->orderReturnId = $orderReturnId;
    }

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
    public function getGoodsReturnNum()
    {
        return $this->goodsReturnNum;
    }

    /**
     * @param mixed $goodsReturnNum
     */
    public function setGoodsReturnNum($goodsReturnNum)
    {
        $this->goodsReturnNum = $goodsReturnNum;
    }

    /**
     * @return mixed
     */
    public function getGoodsReturnAmount()
    {
        return $this->goodsReturnAmount;
    }

    /**
     * @param mixed $goodsReturnAmount
     */
    public function setGoodsReturnAmount($goodsReturnAmount)
    {
        $this->goodsReturnAmount = $goodsReturnAmount;
    }

}