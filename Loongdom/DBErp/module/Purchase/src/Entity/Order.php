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

use Admin\Entity\BaseEntity;
use Customer\Entity\Supplier;
use Doctrine\ORM\Mapping as ORM;

/**
 * 采购订单
 * @package Purchase\Entity
 * @ORM\Entity(repositoryClass="Purchase\Repository\OrderRepository")
 * @ORM\Table(name="dberp_purchase_order")
 */
class Order extends BaseEntity
{
    /**
     * 采购订单id
     * @ORM\Id()
     * @ORM\Column(name="p_order_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $pOrderId;

    /**
     * 采购订单编号
     * @ORM\Column(name="p_order_sn", type="string", length=50)
     */
    private $pOrderSn;

    /**
     * 供应商id
     * @ORM\Column(name="supplier_id", type="integer", length=11)
     */
    private $supplierId;

    /**
     * 联系人
     * @ORM\Column(name="supplier_contacts", type="string", length=30)
     */
    private $supplierContacts;

    /**
     * 联系人手机号码
     * @ORM\Column(name="supplier_phone", type="string", length=20)
     */
    private $supplierPhone;

    /**
     * 联系人座机
     * @ORM\Column(name="supplier_telephone", type="string", length=20)
     */
    private $supplierTelephone;

    /**
     * 采购订单商品总额
     * @ORM\Column(name="p_order_goods_amount", type="decimal", scale=4)
     */
    private $pOrderGoodsAmount;

    /**
     * 采购订单税金
     * @ORM\Column(name="p_order_tax_amount", type="decimal", scale=4)
     */
    private $pOrderTaxAmount;

    /**
     * 采购订单总额
     * @ORM\Column(name="p_order_amount", type="decimal", scale=4)
     */
    private $pOrderAmount;

    /**
     * 采购订单备注
     * @ORM\Column(name="p_order_info", type="string", length=500)
     */
    private $pOrderInfo;

    /**
     * 采购订单状态，0 未审核，1 已审核，2 待入库，3 已入库
     * @ORM\Column(name="p_order_state", type="integer", length=4)
     */
    private $pOrderState;

    /**
     * 付款方式
     * @ORM\Column(name="payment_code", type="string", length=20)
     */
    private $paymentCode;

    /**
     * 退货状态
     * 0 无退货，1 有退货
     * @ORM\Column(name="return_state", type="integer", length=2)
     */
    private $returnState;

    /**
     * 管理员id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * 供应商
     * @ORM\ManyToOne(targetEntity="Customer\Entity\Supplier", inversedBy="orders")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="supplier_id")
     */
    private $oneSupplier;

    public function getOneSupplier()
    {
        return $this->oneSupplier;
    }
    public function setOneSupplier(Supplier $supplier)
    {
        $this->oneSupplier = $supplier;
        $supplier->addOrders($this);
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
    public function getPOrderSn()
    {
        return $this->pOrderSn;
    }

    /**
     * @param mixed $pOrderSn
     */
    public function setPOrderSn($pOrderSn)
    {
        $this->pOrderSn = $pOrderSn;
    }

    /**
     * @return mixed
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * @param mixed $supplierId
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = $supplierId;
    }

    /**
     * @return mixed
     */
    public function getPOrderGoodsAmount()
    {
        return $this->pOrderGoodsAmount;
    }

    /**
     * @param mixed $pOrderGoodsAmount
     */
    public function setPOrderGoodsAmount($pOrderGoodsAmount)
    {
        $this->pOrderGoodsAmount = $pOrderGoodsAmount;
    }

    /**
     * @return mixed
     */
    public function getPOrderTaxAmount()
    {
        return $this->pOrderTaxAmount;
    }

    /**
     * @param mixed $pOrderTaxAmount
     */
    public function setPOrderTaxAmount($pOrderTaxAmount)
    {
        $this->pOrderTaxAmount = $pOrderTaxAmount;
    }

    /**
     * @return mixed
     */
    public function getPOrderAmount()
    {
        return $this->pOrderAmount;
    }

    /**
     * @param mixed $pOrderAmount
     */
    public function setPOrderAmount($pOrderAmount)
    {
        $this->pOrderAmount = $pOrderAmount;
    }

    /**
     * @return mixed
     */
    public function getPOrderInfo()
    {
        return $this->pOrderInfo;
    }

    /**
     * @param mixed $pOrderInfo
     */
    public function setPOrderInfo($pOrderInfo)
    {
        $this->pOrderInfo = $pOrderInfo;
    }

    /**
     * @return mixed
     */
    public function getPOrderState()
    {
        return $this->pOrderState;
    }

    /**
     * @param mixed $pOrderState
     */
    public function setPOrderState($pOrderState)
    {
        $this->pOrderState = $pOrderState;
    }

    /**
     * @return mixed
     */
    public function getPaymentCode()
    {
        return $this->paymentCode;
    }

    /**
     * @param mixed $paymentCode
     */
    public function setPaymentCode($paymentCode)
    {
        $this->paymentCode = $paymentCode;
    }

    /**
     * @return mixed
     */
    public function getSupplierContacts()
    {
        return $this->supplierContacts;
    }

    /**
     * @param mixed $supplierContacts
     */
    public function setSupplierContacts($supplierContacts)
    {
        $this->supplierContacts = $supplierContacts;
    }

    /**
     * @return mixed
     */
    public function getSupplierPhone()
    {
        return $this->supplierPhone;
    }

    /**
     * @param mixed $supplierPhone
     */
    public function setSupplierPhone($supplierPhone)
    {
        $this->supplierPhone = $supplierPhone;
    }

    /**
     * @return mixed
     */
    public function getSupplierTelephone()
    {
        return $this->supplierTelephone;
    }

    /**
     * @param mixed $supplierTelephone
     */
    public function setSupplierTelephone($supplierTelephone)
    {
        $this->supplierTelephone = $supplierTelephone;
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