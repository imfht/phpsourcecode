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
 * 订单发货
 * Class SalesSendOrder
 * @package Sales\Entity
 * @ORM\Entity(repositoryClass="Sales\Repository\SalesSendOrderRepository")
 * @ORM\Table(name="dberp_sales_send_order")
 */
class SalesSendOrder
{
    /**
     * 发货订单ID
     * @ORM\Id()
     * @ORM\Column(name="send_order_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sendOrderId;

    /**
     * 发货订单编号
     * @ORM\Column(name="send_order_sn", type="string", length=50)
     */
    private $sendOrderSn;

    /**
     * 销售订单id
     * @ORM\Column(name="sales_order_id", type="integer", length=11)
     */
    private $salesOrderId;

    /**
     * 是否有退货，0没有，1有
     * @ORM\Column(name="return_state", type="integer", length=2)
     */
    private $returnState;

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
    public function getSendOrderId()
    {
        return $this->sendOrderId;
    }

    /**
     * @param mixed $sendOrderId
     */
    public function setSendOrderId($sendOrderId)
    {
        $this->sendOrderId = $sendOrderId;
    }

    /**
     * @return mixed
     */
    public function getSendOrderSn()
    {
        return $this->sendOrderSn;
    }

    /**
     * @param mixed $sendOrderSn
     */
    public function setSendOrderSn($sendOrderSn)
    {
        $this->sendOrderSn = $sendOrderSn;
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