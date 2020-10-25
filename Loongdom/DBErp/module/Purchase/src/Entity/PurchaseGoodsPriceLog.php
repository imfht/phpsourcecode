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
 * 采购商品价格历史记录
 * Class PurchaseGoodsPriceLog
 * @package Purchase\Entity
 * @ORM\Entity(repositoryClass="Purchase\Repository\PurchaseGoodsPriceLogRepository")
 * @ORM\Table(name="dberp_purchase_goods_price_log")
 */
class PurchaseGoodsPriceLog
{
    /**
     * 自增id
     * @ORM\Id()
     * @ORM\Column(name="price_log_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $priceLogId;

    /**
     * 商品id
     * @ORM\Column(name="goods_id", type="integer", length=11)
     */
    private $goodsId;

    /**
     * 商品价格
     * @ORM\Column(name="goods_price", type="decimal", scale=4)
     */
    private $goodsPrice;

    /**
     * 采购id
     * @ORM\Column(name="p_order_id", type="integer", length=11)
     */
    private $pOrderId;

    /**
     * 历史时间
     * @ORM\Column(name="log_time", type="integer", length=10)
     */
    private $logTime;

    /**
     * @return mixed
     */
    public function getPriceLogId()
    {
        return $this->priceLogId;
    }

    /**
     * @param mixed $priceLogId
     */
    public function setPriceLogId($priceLogId)
    {
        $this->priceLogId = $priceLogId;
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
    public function getLogTime()
    {
        return $this->logTime;
    }

    /**
     * @param mixed $logTime
     */
    public function setLogTime($logTime)
    {
        $this->logTime = $logTime;
    }

}