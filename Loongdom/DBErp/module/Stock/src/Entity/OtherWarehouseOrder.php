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
 * Class OtherWarehouseOrder
 * @package Stock\Entity
 * @ORM\Entity(repositoryClass="Stock\Repository\OtherWarehouseOrderRepository")
 * @ORM\Table(name="dberp_other_warehouse_order")
 */
class OtherWarehouseOrder extends BaseEntity
{
    /**
     * 入库单id
     * @ORM\Id()
     * @ORM\Column(name="other_warehouse_order_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $otherWarehouseOrderId;

    /**
     * 仓库id
     * @ORM\Column(name="warehouse_id", type="integer", length=11)
     */
    private $warehouseId;

    /**
     * 入库单编号
     * @ORM\Column(name="warehouse_order_sn", type="string", length=50)
     */
    private $warehouseOrderSn;

    /**
     * 入库单状态，3 已入库
     * @ORM\Column(name="warehouse_order_state", type="integer", length=1)
     */
    private $warehouseOrderState;

    /**
     * 入库单备注信息
     * @ORM\Column(name="warehouse_order_info", type="string", length=255)
     */
    private $warehouseOrderInfo;

    /**
     * 入库单商品总价
     * @ORM\Column(name="warehouse_order_goods_amount", type="decimal", scale=4)
     */
    private $warehouseOrderGoodsAmount;

    /**
     * 入库单税金
     * @ORM\Column(name="warehouse_order_tax", type="decimal", scale=4)
     */
    private $warehouseOrderTax;

    /**
     * 入库单总金额
     * @ORM\Column(name="warehouse_order_amount", type="decimal", scale=4)
     */
    private $warehouseOrderAmount;

    /**
     * 入库时间
     * @ORM\Column(name="other_add_time", type="integer", length=10)
     *
     */
    private $otherAddTime;

    /**
     * 管理员id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * @var
     * @ORM\OneToOne(targetEntity="Store\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="warehouse_id")
     */
    private $oneWarehouse;

    /**
     * @return mixed
     */
    public function getOneWarehouse()
    {
        return $this->oneWarehouse;
    }

    /**
     * @param mixed $oneWarehouse
     */
    public function setOneWarehouse($oneWarehouse)
    {
        $this->oneWarehouse = $oneWarehouse;
    }

    /**
     * @return mixed
     */
    public function getOtherWarehouseOrderId()
    {
        return $this->otherWarehouseOrderId;
    }

    /**
     * @param mixed $otherWarehouseOrderId
     */
    public function setOtherWarehouseOrderId($otherWarehouseOrderId)
    {
        $this->otherWarehouseOrderId = $otherWarehouseOrderId;
    }

    /**
     * @return mixed
     */
    public function getWarehouseId()
    {
        return $this->warehouseId;
    }

    /**
     * @param mixed $warehouseId
     */
    public function setWarehouseId($warehouseId)
    {
        $this->warehouseId = $warehouseId;
    }

    /**
     * @return mixed
     */
    public function getWarehouseOrderSn()
    {
        return $this->warehouseOrderSn;
    }

    /**
     * @param mixed $warehouseOrderSn
     */
    public function setWarehouseOrderSn($warehouseOrderSn)
    {
        $this->warehouseOrderSn = $warehouseOrderSn;
    }

    /**
     * @return mixed
     */
    public function getWarehouseOrderState()
    {
        return $this->warehouseOrderState;
    }

    /**
     * @param mixed $warehouseOrderState
     */
    public function setWarehouseOrderState($warehouseOrderState)
    {
        $this->warehouseOrderState = $warehouseOrderState;
    }

    /**
     * @return mixed
     */
    public function getWarehouseOrderInfo()
    {
        return $this->warehouseOrderInfo;
    }

    /**
     * @param mixed $warehouseOrderInfo
     */
    public function setWarehouseOrderInfo($warehouseOrderInfo)
    {
        $this->warehouseOrderInfo = $warehouseOrderInfo;
    }

    /**
     * @return mixed
     */
    public function getWarehouseOrderGoodsAmount()
    {
        return $this->warehouseOrderGoodsAmount;
    }

    /**
     * @param mixed $warehouseOrderGoodsAmount
     */
    public function setWarehouseOrderGoodsAmount($warehouseOrderGoodsAmount)
    {
        $this->warehouseOrderGoodsAmount = $warehouseOrderGoodsAmount;
    }

    /**
     * @return mixed
     */
    public function getWarehouseOrderTax()
    {
        return $this->warehouseOrderTax;
    }

    /**
     * @param mixed $warehouseOrderTax
     */
    public function setWarehouseOrderTax($warehouseOrderTax)
    {
        $this->warehouseOrderTax = $warehouseOrderTax;
    }

    /**
     * @return mixed
     */
    public function getWarehouseOrderAmount()
    {
        return $this->warehouseOrderAmount;
    }

    /**
     * @param mixed $warehouseOrderAmount
     */
    public function setWarehouseOrderAmount($warehouseOrderAmount)
    {
        $this->warehouseOrderAmount = $warehouseOrderAmount;
    }

    /**
     * @return mixed
     */
    public function getOtherAddTime()
    {
        return $this->otherAddTime;
    }

    /**
     * @param mixed $otherAddTime
     */
    public function setOtherAddTime($otherAddTime)
    {
        $this->otherAddTime = $otherAddTime;
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