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
 * 应付款
 * @package Finance\Entity
 * @ORM\Entity(repositoryClass="Finance\Repository\PayableRepository")
 * @ORM\Table(name="dberp_finance_payable")
 */
class Payable extends BaseEntity
{
    /**
     * 付款id
     * @ORM\Id()
     * @ORM\Column(name="Payable_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $payableId;

    /**
     * 入库单id
     * @ORM\Column(name="warehouse_order_id", type="integer", length=11)
     *
     */
    private $warehouseOrderId;

    /**
     * 采购订单id
     * @ORM\Column(name="p_order_id", type="integer", length=11)
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
     * 供应商名称
     * @ORM\Column(name="supplier_name", type="string", length=100)
     */
    private $supplierName;

    /**
     * 支付方式code
     * @ORM\Column(name="payment_code", type="string", length=20)
     */
    private $paymentCode;

    /**
     * 应付金额
     * @ORM\Column(name="payment_amount", type="decimal", scale=4)
     */
    private $paymentAmount;

    /**
     * 已付款金额
     * @ORM\Column(name="finish_amount", type="decimal", scale=4)
     */
    private $finishAmount;

    /**
     * 添加时间
     * @ORM\Column(name="add_time", type="integer", length=10)
     */
    private $addTime;

    /**
     * 管理员id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * @return mixed
     */
    public function getPayableId()
    {
        return $this->payableId;
    }

    /**
     * @param mixed $payableId
     */
    public function setPayableId($payableId)
    {
        $this->payableId = $payableId;
    }

    /**
     * @return mixed
     */
    public function getWarehouseOrderId()
    {
        return $this->warehouseOrderId;
    }

    /**
     * @param mixed $warehouseOrderId
     */
    public function setWarehouseOrderId($warehouseOrderId)
    {
        $this->warehouseOrderId = $warehouseOrderId;
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
    public function getSupplierName()
    {
        return $this->supplierName;
    }

    /**
     * @param mixed $supplierName
     */
    public function setSupplierName($supplierName)
    {
        $this->supplierName = $supplierName;
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
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * @param mixed $paymentAmount
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;
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