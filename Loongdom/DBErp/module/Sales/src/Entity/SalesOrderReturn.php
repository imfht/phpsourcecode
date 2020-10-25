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
 * 销售退货单表
 * Class SalesOrderReturn
 * @package Sales\Entity
 * @ORM\Entity(repositoryClass="Sales\Repository\SalesOrderReturnRepository")
 * @ORM\Table(name="dberp_sales_order_return")
 */
class SalesOrderReturn
{
    /**
     * 自增id
     * @ORM\Id()
     * @ORM\Column(name="sales_order_return_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $salesOrderReturnId;

    /**
     * 销售单id
     * @ORM\Column(name="sales_order_id", type="integer", length=11)
     */
    private $salesOrderId;

    /**
     * 销售单号
     * @ORM\Column(name="sales_order_sn", type="string", length=50)
     */
    private $salesOrderSn;

    /**
     *
     * @ORM\Column(name="sales_send_order_id", type="integer", length=11)
     */
    private $salesSendOrderId;

    /**
     * 发货单号
     * @ORM\Column(name="sales_send_order_sn", type="string", length=50)
     */
    private $salesSendOrderSn;

    /**
     * 退货商品金额
     * @ORM\Column(name="sales_order_goods_return_amount", type="decimal", scale=4)
     */
    private $salesOrderGoodsReturnAmount;

    /**
     * 退货金额
     * @ORM\Column(name="sales_order_return_amount", type="decimal", scale=4)
     */
    private $salesOrderReturnAmount;

    /**
     *
     * @ORM\Column(name="sales_order_return_info", type="string", length=500)
     */
    private $salesOrderReturnInfo;

    /**
     * 退货时间
     * @ORM\Column(name="return_time", type="integer", length=10)
     */
    private $returnTime;

    /**
     * 退货状态
     * @ORM\Column(name="return_state", type="integer", length=2)
     */
    private $returnState;

    /**
     * 退货完成时间
     * @ORM\Column(name="return_finish_time", type="integer", length=10)
     */
    private $returnFinishTime;

    /**
     * 操作者id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * 一对一，销售订单
     * @ORM\OneToOne(targetEntity="Sales\Entity\SalesOrder")
     * @ORM\JoinColumn(name="sales_order_id", referencedColumnName="sales_order_id")
     */
    private $oneSalesOrder;

    /**
     * @return mixed
     */
    public function getOneSalesOrder()
    {
        return $this->oneSalesOrder;
    }

    /**
     * @param mixed $oneSalesOrder
     */
    public function setOneSalesOrder($oneSalesOrder)
    {
        $this->oneSalesOrder = $oneSalesOrder;
    }

    /**
     * @return mixed
     */
    public function getSalesOrderReturnId()
    {
        return $this->salesOrderReturnId;
    }

    /**
     * @param mixed $salesOrderReturnId
     */
    public function setSalesOrderReturnId($salesOrderReturnId)
    {
        $this->salesOrderReturnId = $salesOrderReturnId;
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
    public function getSalesOrderSn()
    {
        return $this->salesOrderSn;
    }

    /**
     * @param mixed $salesOrderSn
     */
    public function setSalesOrderSn($salesOrderSn)
    {
        $this->salesOrderSn = $salesOrderSn;
    }

    /**
     * @return mixed
     */
    public function getSalesSendOrderId()
    {
        return $this->salesSendOrderId;
    }

    /**
     * @param mixed $salesSendOrderId
     */
    public function setSalesSendOrderId($salesSendOrderId)
    {
        $this->salesSendOrderId = $salesSendOrderId;
    }

    /**
     * @return mixed
     */
    public function getSalesSendOrderSn()
    {
        return $this->salesSendOrderSn;
    }

    /**
     * @param mixed $salesSendOrderSn
     */
    public function setSalesSendOrderSn($salesSendOrderSn)
    {
        $this->salesSendOrderSn = $salesSendOrderSn;
    }

    /**
     * @return mixed
     */
    public function getSalesOrderGoodsReturnAmount()
    {
        return $this->salesOrderGoodsReturnAmount;
    }

    /**
     * @param mixed $salesOrderGoodsReturnAmount
     */
    public function setSalesOrderGoodsReturnAmount($salesOrderGoodsReturnAmount)
    {
        $this->salesOrderGoodsReturnAmount = $salesOrderGoodsReturnAmount;
    }

    /**
     * @return mixed
     */
    public function getSalesOrderReturnAmount()
    {
        return $this->salesOrderReturnAmount;
    }

    /**
     * @param mixed $salesOrderReturnAmount
     */
    public function setSalesOrderReturnAmount($salesOrderReturnAmount)
    {
        $this->salesOrderReturnAmount = $salesOrderReturnAmount;
    }

    /**
     * @return mixed
     */
    public function getSalesOrderReturnInfo()
    {
        return $this->salesOrderReturnInfo;
    }

    /**
     * @param mixed $salesOrderReturnInfo
     */
    public function setSalesOrderReturnInfo($salesOrderReturnInfo)
    {
        $this->salesOrderReturnInfo = $salesOrderReturnInfo;
    }

    /**
     * @return mixed
     */
    public function getReturnTime()
    {
        return $this->returnTime;
    }

    /**
     * @param mixed $returnTime
     */
    public function setReturnTime($returnTime)
    {
        $this->returnTime = $returnTime;
    }

    /**
     * @return mixed
     */
    public function getReturnState()
    {
        return $this->returnState;
    }

    /**
     * @param mixed $returnState
     */
    public function setReturnState($returnState)
    {
        $this->returnState = $returnState;
    }

    /**
     * @return mixed
     */
    public function getReturnFinishTime()
    {
        return $this->returnFinishTime;
    }

    /**
     * @param mixed $returnFinishTime
     */
    public function setReturnFinishTime($returnFinishTime)
    {
        $this->returnFinishTime = $returnFinishTime;
    }

    /**
     * @return mixed
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * @param mixed $adminId
     */
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
    }

}