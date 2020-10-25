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

namespace Finance\Entity;

use Admin\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * 应收账款
 * Class Receivable
 * @package Finance\Entity
 * @ORM\Entity(repositoryClass="Finance\Repository\ReceivableRepository")
 * @ORM\Table(name="dberp_accounts_receivable")
 */
class Receivable extends BaseEntity
{
    /**
     * 自增id
     * @ORM\Id()
     * @ORM\Column(name="receivable_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $receivableId;

    /**
     * 销售订单id
     * @ORM\Column(name="sales_order_id", type="integer", length=11)
     */
    private $salesOrderId;

    /**
     * 销售订单号
     * @ORM\Column(name="sales_order_sn", type="string", length=50)
     */
    private $salesOrderSn;

    /**
     * 发货订单id
     * @ORM\Column(name="send_order_id", type="integer", length=11)
     */
    private $sendOrderId;

    /**
     * 发货定单号
     * @ORM\Column(name="send_order_sn", type="string", length=50)
     */
    private $sendOrderSn;

    /**
     * 客户id
     * @ORM\Column(name="customer_id", type="integer", length=11)
     */
    private $customerId;

    /**
     * 客户名称
     * @ORM\Column(name="customer_name", type="string", length=100)
     */
    private $customerName;

    /**
     * 收款方式
     * @ORM\Column(name="receivable_code", type="string", length=20)
     */
    private $receivableCode;

    /**
     * 应收金额
     * @ORM\Column(name="receivable_amount", type="decimal", scale=4)
     */
    private $receivableAmount;

    /**
     * 已收款金额
     * @ORM\Column(name="finish_amount", type="decimal", scale=4)
     */
    private $finishAmount;

    /**
     * 添加时间
     * @ORM\Column(name="add_time", type="integer", length=10)
     */
    private $addTime;

    /**
     * 添加者id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * @return mixed
     */
    public function getReceivableId()
    {
        return $this->receivableId;
    }

    /**
     * @param mixed $receivableId
     */
    public function setReceivableId($receivableId)
    {
        $this->receivableId = $receivableId;
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
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param mixed $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return mixed
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * @param mixed $customerName
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;
    }

    /**
     * @return mixed
     */
    public function getReceivableCode()
    {
        return $this->receivableCode;
    }

    /**
     * @param mixed $receivableCode
     */
    public function setReceivableCode($receivableCode)
    {
        $this->receivableCode = $receivableCode;
    }

    /**
     * @return mixed
     */
    public function getReceivableAmount()
    {
        return $this->receivableAmount;
    }

    /**
     * @param mixed $receivableAmount
     */
    public function setReceivableAmount($receivableAmount)
    {
        $this->receivableAmount = $receivableAmount;
    }

    /**
     * @return mixed
     */
    public function getFinishAmount()
    {
        return $this->finishAmount;
    }

    /**
     * @param mixed $finishAmount
     */
    public function setFinishAmount($finishAmount)
    {
        $this->finishAmount = $finishAmount;
    }

    /**
     * @return mixed
     */
    public function getAddTime()
    {
        return $this->addTime;
    }

    /**
     * @param mixed $addTime
     */
    public function setAddTime($addTime)
    {
        $this->addTime = $addTime;
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