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

use Doctrine\ORM\Mapping as ORM;

/**
 * 商城订单
 * Class ShopOrder
 * @package Shop\Entity
 * @ORM\Entity(repositoryClass="Shop\Repository\ShopOrderRepository")
 * @ORM\Table(name="dberp_shop_order")
 */
class ShopOrder
{
    /**
     * 自增id
     * @ORM\Id()
     * @ORM\Column(name="shop_order_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $shopOrderId;

    /**
     * 订单编号
     * @ORM\Column(name="shop_order_sn", type="string", length=50)
     */
    private $shopOrderSn;

    /**
     * 订单支付方式编码
     * @ORM\Column(name="shop_buy_name", type="string", length=100)
     */
    private $shopBuyName;

    /**
     *
     * @ORM\Column(name="shop_payment_code", type="string",scale=20)
     */
    private $shopPaymentCode;

    /**
     * 订单支付名称
     * @ORM\Column(name="shop_payment_name", type="string", length=30)
     */
    private $shopPaymentName;

    /**
     * 订单支付费用
     * @ORM\Column(name="shop_payment_cost", type="decimal", scale=4)
     */
    private $shopPaymentCost;

    /**
     * 订单支付凭证信息
     * @ORM\Column(name="shop_payment_certification", type="string", length=500)
     */
    private $shopPaymentCertification;

    /**
     * 订单配送方式代码
     * @ORM\Column(name="shop_express_code", type="string", length=30)
     */
    private $shopExpressCode;

    /**
     * 订单配送方式名称
     * @ORM\Column(name="shop_express_name", type="string", length=50)
     */
    private $shopExpressName;

    /**
     * 订单配送费用
     * @ORM\Column(name="shop_express_cost", type="decimal", scale=4)
     */
    private $shopExpressCost;

    /**
     * 订单其他费用
     * @ORM\Column(name="shop_order_other_cost", type="decimal", scale=4)
     */
    private $shopOrderOtherCost;

    /**
     * 订单其他费用描述
     * @ORM\Column(name="shop_order_other_info", type="string", length=500)
     */
    private $shopOrderOtherInfo;

    /**
     * 订单状态
     * @ORM\Column(name="shop_order_state", type="integer", length=2)
     */
    private $shopOrderState;

    /**
     * 订单优惠金额
     * @ORM\Column(name="shop_order_discount_amount", type="decimal", scale=4)
     */
    private $shopOrderDiscountAmount;

    /**
     * 订单优惠描述
     * @ORM\Column(name="shop_order_discount_info", type="string", length=500)
     */
    private $shopOrderDiscountInfo;

    /**
     * 订单商品金额
     * @ORM\Column(name="shop_order_goods_amount", type="decimal", scale=4)
     */
    private $shopOrderGoodsAmount;

    /**
     * 订单金额
     * @ORM\Column(name="shop_order_amount", type="decimal", scale=4)
     */
    private $shopOrderAmount;

    /**
     * 订单添加时间
     * @ORM\Column(name="shop_order_add_time", type="integer", length=10)
     */
    private $shopOrderAddTime;

    /**
     * 订单支付时间
     * @ORM\Column(name="shop_order_pay_time", type="integer", length=11)
     */
    private $shopOrderPayTime;

    /**
     * 订单发货时间
     * @ORM\Column(name="shop_order_express_time", type="integer", length=11)
     */
    private $shopOrderExpressTime;

    /**
     * 订单完成时间
     * @ORM\Column(name="shop_order_finish_time", type="integer", length=11)
     */
    private $shopOrderFinishTime;

    /**
     * 订单留言
     * @ORM\Column(name="shop_order_message", type="string", length=500)
     */
    private $shopOrderMessage;

    /**
     * 绑定的商城应用id
     * @ORM\Column(name="app_id", type="integer", length=11)
     */
    private $appId;

    /**
     *
     * @ORM\OneToOne(targetEntity="Admin\Entity\App")
     * @ORM\JoinColumn(name="app_id", referencedColumnName="app_id")
     */
    private $oneApp;

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
    public function getShopOrderSn()
    {
        return $this->shopOrderSn;
    }

    /**
     * @param mixed $shopOrderSn
     */
    public function setShopOrderSn($shopOrderSn)
    {
        $this->shopOrderSn = $shopOrderSn;
    }

    /**
     * @return mixed
     */
    public function getShopBuyName()
    {
        return $this->shopBuyName;
    }

    /**
     * @param mixed $shopBuyName
     */
    public function setShopBuyName($shopBuyName)
    {
        $this->shopBuyName = $shopBuyName;
    }

    /**
     * @return mixed
     */
    public function getShopPaymentCode()
    {
        return $this->shopPaymentCode;
    }

    /**
     * @param mixed $shopPaymentCode
     */
    public function setShopPaymentCode($shopPaymentCode)
    {
        $this->shopPaymentCode = $shopPaymentCode;
    }

    /**
     * @return mixed
     */
    public function getShopPaymentName()
    {
        return $this->shopPaymentName;
    }

    /**
     * @param mixed $shopPaymentName
     */
    public function setShopPaymentName($shopPaymentName)
    {
        $this->shopPaymentName = $shopPaymentName;
    }

    /**
     * @return mixed
     */
    public function getShopPaymentCost()
    {
        return $this->shopPaymentCost;
    }

    /**
     * @param mixed $shopPaymentCost
     */
    public function setShopPaymentCost($shopPaymentCost)
    {
        $this->shopPaymentCost = $shopPaymentCost;
    }

    /**
     * @return mixed
     */
    public function getShopPaymentCertification()
    {
        return $this->shopPaymentCertification;
    }

    /**
     * @param mixed $shopPaymentCertification
     */
    public function setShopPaymentCertification($shopPaymentCertification)
    {
        $this->shopPaymentCertification = $shopPaymentCertification;
    }

    /**
     * @return mixed
     */
    public function getShopExpressCode()
    {
        return $this->shopExpressCode;
    }

    /**
     * @param mixed $shopExpressCode
     */
    public function setShopExpressCode($shopExpressCode)
    {
        $this->shopExpressCode = $shopExpressCode;
    }

    /**
     * @return mixed
     */
    public function getShopExpressName()
    {
        return $this->shopExpressName;
    }

    /**
     * @param mixed $shopExpressName
     */
    public function setShopExpressName($shopExpressName)
    {
        $this->shopExpressName = $shopExpressName;
    }

    /**
     * @return mixed
     */
    public function getShopExpressCost()
    {
        return $this->shopExpressCost;
    }

    /**
     * @param mixed $shopExpressCost
     */
    public function setShopExpressCost($shopExpressCost)
    {
        $this->shopExpressCost = $shopExpressCost;
    }

    /**
     * @return mixed
     */
    public function getShopOrderOtherCost()
    {
        return $this->shopOrderOtherCost;
    }

    /**
     * @param mixed $shopOrderOtherCost
     */
    public function setShopOrderOtherCost($shopOrderOtherCost)
    {
        $this->shopOrderOtherCost = $shopOrderOtherCost;
    }

    /**
     * @return mixed
     */
    public function getShopOrderOtherInfo()
    {
        return $this->shopOrderOtherInfo;
    }

    /**
     * @param mixed $shopOrderOtherInfo
     */
    public function setShopOrderOtherInfo($shopOrderOtherInfo)
    {
        $this->shopOrderOtherInfo = $shopOrderOtherInfo;
    }

    /**
     * @return mixed
     */
    public function getShopOrderState()
    {
        return $this->shopOrderState;
    }

    /**
     * @param mixed $shopOrderState
     */
    public function setShopOrderState($shopOrderState)
    {
        $this->shopOrderState = $shopOrderState;
    }

    /**
     * @return mixed
     */
    public function getShopOrderDiscountAmount()
    {
        return $this->shopOrderDiscountAmount;
    }

    /**
     * @param mixed $shopOrderDiscountAmount
     */
    public function setShopOrderDiscountAmount($shopOrderDiscountAmount)
    {
        $this->shopOrderDiscountAmount = $shopOrderDiscountAmount;
    }

    /**
     * @return mixed
     */
    public function getShopOrderDiscountInfo()
    {
        return $this->shopOrderDiscountInfo;
    }

    /**
     * @param mixed $shopOrderDiscountInfo
     */
    public function setShopOrderDiscountInfo($shopOrderDiscountInfo)
    {
        $this->shopOrderDiscountInfo = $shopOrderDiscountInfo;
    }

    /**
     * @return mixed
     */
    public function getShopOrderGoodsAmount()
    {
        return $this->shopOrderGoodsAmount;
    }

    /**
     * @param mixed $shopOrderGoodsAmount
     */
    public function setShopOrderGoodsAmount($shopOrderGoodsAmount)
    {
        $this->shopOrderGoodsAmount = $shopOrderGoodsAmount;
    }

    /**
     * @return mixed
     */
    public function getShopOrderAmount()
    {
        return $this->shopOrderAmount;
    }

    /**
     * @param mixed $shopOrderAmount
     */
    public function setShopOrderAmount($shopOrderAmount)
    {
        $this->shopOrderAmount = $shopOrderAmount;
    }

    /**
     * @return mixed
     */
    public function getShopOrderAddTime()
    {
        return $this->shopOrderAddTime;
    }

    /**
     * @param mixed $shopOrderAddTime
     */
    public function setShopOrderAddTime($shopOrderAddTime)
    {
        $this->shopOrderAddTime = $shopOrderAddTime;
    }

    /**
     * @return mixed
     */
    public function getShopOrderPayTime()
    {
        return $this->shopOrderPayTime;
    }

    /**
     * @param mixed $shopOrderPayTime
     */
    public function setShopOrderPayTime($shopOrderPayTime)
    {
        $this->shopOrderPayTime = $shopOrderPayTime;
    }

    /**
     * @return mixed
     */
    public function getShopOrderExpressTime()
    {
        return $this->shopOrderExpressTime;
    }

    /**
     * @param mixed $shopOrderExpressTime
     */
    public function setShopOrderExpressTime($shopOrderExpressTime)
    {
        $this->shopOrderExpressTime = $shopOrderExpressTime;
    }

    /**
     * @return mixed
     */
    public function getShopOrderFinishTime()
    {
        return $this->shopOrderFinishTime;
    }

    /**
     * @param mixed $shopOrderFinishTime
     */
    public function setShopOrderFinishTime($shopOrderFinishTime)
    {
        $this->shopOrderFinishTime = $shopOrderFinishTime;
    }

    /**
     * @return mixed
     */
    public function getShopOrderMessage()
    {
        return $this->shopOrderMessage;
    }

    /**
     * @param mixed $shopOrderMessage
     */
    public function setShopOrderMessage($shopOrderMessage)
    {
        $this->shopOrderMessage = $shopOrderMessage;
    }

    /**
     * @return mixed
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param mixed $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return mixed
     */
    public function getOneApp()
    {
        return $this->oneApp;
    }

    /**
     * @param mixed $oneApp
     */
    public function setOneApp($oneApp)
    {
        $this->oneApp = $oneApp;
    }

}