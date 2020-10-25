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
 * 库存盘点商品
 * @package Stock\Entity
 * @ORM\Entity(repositoryClass="Stock\Repository\StockCheckGoodsRepository")
 * @ORM\Table(name="dberp_stock_check_goods")
 */
class StockCheckGoods extends BaseEntity
{
    /**
     * 自增id
     * @ORM\Id()
     * @ORM\Column(name="stock_check_goods_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $stockCheckGoodsId;

    /**
     * 库存盘点id
     * @ORM\Column(name="stock_check_id", type="integer", length=11)
     */
    private $stockCheckId;

    /**
     * 库存盘点前数量
     * @ORM\Column(name="stock_check_pre_goods_num", type="integer", length=11)
     */
    private $stockCheckPreGoodsNum;

    /**
     * 库存盘点后数量
     * @ORM\Column(name="stock_check_aft_goods_num", type="integer", length=11)
     */
    private $stockCheckAftGoodsNum;

    /**
     * 库存盘点商品金额
     * @ORM\Column(name="stock_check_goods_amount", type="decimal", scale=4)
     */
    private $stockCheckGoodsAmount;

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
    public function getStockCheckGoodsId()
    {
        return $this->stockCheckGoodsId;
    }

    /**
     * @param mixed $stockCheckGoodsId
     */
    public function setStockCheckGoodsId($stockCheckGoodsId)
    {
        $this->stockCheckGoodsId = $stockCheckGoodsId;
    }

    /**
     * @return mixed
     */
    public function getStockCheckId()
    {
        return $this->stockCheckId;
    }

    /**
     * @param mixed $stockCheckId
     */
    public function setStockCheckId($stockCheckId)
    {
        $this->stockCheckId = $stockCheckId;
    }

    /**
     * @return mixed
     */
    public function getStockCheckPreGoodsNum()
    {
        return $this->stockCheckPreGoodsNum;
    }

    /**
     * @param mixed $stockCheckPreGoodsNum
     */
    public function setStockCheckPreGoodsNum($stockCheckPreGoodsNum)
    {
        $this->stockCheckPreGoodsNum = $stockCheckPreGoodsNum;
    }

    /**
     * @return mixed
     */
    public function getStockCheckAftGoodsNum()
    {
        return $this->stockCheckAftGoodsNum;
    }

    /**
     * @param mixed $stockCheckAftGoodsNum
     */
    public function setStockCheckAftGoodsNum($stockCheckAftGoodsNum)
    {
        $this->stockCheckAftGoodsNum = $stockCheckAftGoodsNum;
    }

    /**
     * @return mixed
     */
    public function getStockCheckGoodsAmount()
    {
        return $this->stockCheckGoodsAmount;
    }

    /**
     * @param mixed $stockCheckGoodsAmount
     */
    public function setStockCheckGoodsAmount($stockCheckGoodsAmount)
    {
        $this->stockCheckGoodsAmount = $stockCheckGoodsAmount;
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