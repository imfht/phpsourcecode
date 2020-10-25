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

use Admin\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * 销售订单
 * Class SalesOrder
 * @package Sales\Entity
 * @ORM\Entity(repositoryClass="Sales\Repository\SalesOrderRepository")
 * @ORM\Table(name="dberp_sales_order")
 */
class SalesOrder extends BaseEntity
{
    /**
     * 销售订单id
     * @ORM\Id()
     * @ORM\Column(name="sales_order_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $salesOrderId;

    /**
     * 销售订单编号
     * @ORM\Column(name="sales_order_sn", type="string", length=50)
     */
    private $salesOrderSn;

    /**
     * 客户id
     * @ORM\Column(name="customer_id", type="integer", length=11)
     */
    private $customerId;

    /**
     * 联系人
     * @ORM\Column(name="customer_contacts", type="string", length=30)
     */
    private $customerContacts;

    /**
     * 地址
     * @ORM\Column(name="customer_address", type="string", length=255)
     */
    private $customerAddress;

    /**
     * 手机
     * @ORM\Column(name="customer_phone", type="string", length=20)
     */
    private $customerPhone;

    /**
     * 座机
     * @ORM\Column(name="customer_telephone", type="string", length=20)
     */
    private $customerTelephone;

    /**
     * 商品总额
     * @ORM\Column(name="sales_order_goods_amount", type="decimal", scale=4)
     */
    private $salesOrderGoodsAmount;

    /**
     * 税金
     * @ORM\Column(name="sales_order_tax_amount", type="decimal", scale=4)
     */
    private $salesOrderTaxAmount;

    /**
     * 订单总额
     * @ORM\Column(name="sales_order_amount", type="decimal", scale=4)
     */
    private $salesOrderAmount;

    /**
     * 支付方式
     * @ORM\Column(name="receivables_code", type="string", length=20)
     */
    private $receivablesCode;

    /**
     * 销售状态
     * @ORM\Column(name="sales_order_state", type="integer", length=4)
     */
    private $salesOrderState;

    /**
     * 备注
     * @ORM\Column(name="sales_order_info", type="string", length=500)
     */
    private $salesOrderInfo;

    /**
     * 是否存在退货
     * @ORM\Column(name="return_state", type="integer", length=2)
     */
    private $returnState;

    /**
     * 操作者id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * @var
     * @ORM\OneToOne(targetEntity="Customer\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="customer_id")
     */
    private $oneCustomer;

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
    public function getCustomerContacts()
    {
        return $this->customerContacts;
    }

    /**
     * @param mixed $customerContacts
     */
    public function setCustomerContacts($customerContacts)
    {
        $this->customerContacts = $customerContacts;
    }

    /**
     * @return mixed
     */
    public function getCustomerAddress()
    {
        return $this->customerAddress;
    }

    /**
     * @param mixed $customerAddress
     */
    public function setCustomerAddress($customerAddress)
    {
        $this->customerAddress = $customerAddress;
    }

    /**
     * @return mixed
     */
    public function getCustomerPhone()
    {
        return $this->customerPhone;
    }

    /**
     * @param mixed $customerPhone
     */
    public function setCustomerPhone($customerPhone)
    {
        $this->customerPhone = $customerPhone;
    }

    /**
     * @return mixed
     */
    public function getCustomerTelephone()
    {
        return $this->customerTelephone;
    }

    /**
     * @param mixed $customerTelephone
     */
    public function setCustomerTelephone($customerTelephone)
    {
        $this->customerTelephone = $customerTelephone;
    }

    /**
     * @return mixed
     */
    public function getSalesOrderGoodsAmount()
    {
        return $this->salesOrderGoodsAmount;
    }

    /**
     * @param mixed $salesOrderGoodsAmount
     */
    public function setSalesOrderGoodsAmount($salesOrderGoodsAmount)
    {
        $this->salesOrderGoodsAmount = $salesOrderGoodsAmount;
    }

    /**
     * @return mixed
     */
    public function getSalesOrderTaxAmount()
    {
        return $this->salesOrderTaxAmount;
    }

    /**
     * @param mixed $salesOrderTaxAmount
     */
    public function setSalesOrderTaxAmount($salesOrderTaxAmount)
    {
        $this->salesOrderTaxAmount = $salesOrderTaxAmount;
    }

    /**
     * @return mixed
     */
    public function getSalesOrderAmount()
    {
        return $this->salesOrderAmount;
    }

    /**
     * @param mixed $salesOrderAmount
     */
    public function setSalesOrderAmount($salesOrderAmount)
    {
        $this->salesOrderAmount = $salesOrderAmount;
    }

    /**
     * @return mixed
     */
    public function getReceivablesCode()
    {
        return $this->receivablesCode;
    }

    /**
     * @param mixed $receivablesCode
     */
    public function setReceivablesCode($receivablesCode)
    {
        $this->receivablesCode = $receivablesCode;
    }

    /**
     * @return mixed
     */
    public function getSalesOrderState()
    {
        return $this->salesOrderState;
    }

    /**
     * @param mixed $salesOrderState
     */
    public function setSalesOrderState($salesOrderState)
    {
        $this->salesOrderState = $salesOrderState;
    }

    /**
     * @return mixed
     */
    public function getSalesOrderInfo()
    {
        return $this->salesOrderInfo;
    }

    /**
     * @param mixed $salesOrderInfo
     */
    public function setSalesOrderInfo($salesOrderInfo)
    {
        $this->salesOrderInfo = $salesOrderInfo;
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

    /**
     * @return mixed
     */
    public function getOneCustomer()
    {
        return $this->oneCustomer;
    }

    /**
     * @param mixed $oneCustomer
     */
    public function setOneCustomer($oneCustomer)
    {
        $this->oneCustomer = $oneCustomer;
    }
}