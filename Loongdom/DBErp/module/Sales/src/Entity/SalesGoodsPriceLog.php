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
 * 销售商品价格历史记录
 * Class SalesGoodsPriceLog
 * @package Sales\Entity
 * @ORM\Entity(repositoryClass="Sales\Repository\SalesGoodsPriceLogRepository")
 * @ORM\Table(name="dberp_sales_goods_price_log")
 */
class SalesGoodsPriceLog
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
     * 商品销售单价
     * @ORM\Column(name="goods_price", type="decimal", scale=4)
     */
    private $goodsPrice;

    /**
     * 销售订单id
     * @ORM\Column(name="sales_order_id", type="integer", length=11)
     */
    private $salesOrderId;

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