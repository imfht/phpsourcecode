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
 * 库存盘点
 * @package Stock\Entity
 * @ORM\Entity(repositoryClass="Stock\Repository\StockCheckRepository")
 * @ORM\Table(name="dberp_stock_check")
 */
class StockCheck extends BaseEntity
{
    /**
     * 自增id
     * @ORM\Id()
     * @ORM\Column(name="stock_check_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $stockCheckId;

    /**
     * 盘点订单号
     * @ORM\Column(name="stock_check_sn", type="string", length=50)
     */
    private $stockCheckSn;

    /**
     * 仓库id
     * @ORM\Column(name="warehouse_id", type="integer", length=11)
     */
    private $warehouseId;

    /**
     * 盘点金额
     * @ORM\Column(name="stock_check_amount", type="decimal", scale=4)
     */
    private $stockCheckAmount;

    /**
     * 盘点人
     * @ORM\Column(name="stock_check_user", type="string", length=100)
     */
    private $stockCheckUser;

    /**
     * 盘点备注
     * @ORM\Column(name="stock_check_info", type="string", length=255)
     */
    private $stockCheckInfo;

    /**
     * 盘点时间
     * @ORM\Column(name="stock_check_time", type="integer", length=10)
     */
    private $stockCheckTime;

    /**
     * 盘点状态，1 已盘点，2 待盘点
     * @ORM\Column(name="stock_check_state", type="integer", length=1)
     */
    private $stockCheckState;

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
    public function getStockCheckId()
    {
        return $this->stockCheckId;
    }

    /**
     * @param mixed $stockCheckId
     */
    public function setStockCheckId($stockCheckId)
    {
        $this->stockCheckId = $stockCheckId;
    }

    /**
     * @return mixed
     */
    public function getStockCheckSn()
    {
        return $this->stockCheckSn;
    }

    /**
     * @param mixed $stockCheckSn
     */
    public function setStockCheckSn($stockCheckSn)
    {
        $this->stockCheckSn = $stockCheckSn;
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
    public function getStockCheckAmount()
    {
        return $this->stockCheckAmount;
    }

    /**
     * @param mixed $stockCheckAmount
     */
    public function setStockCheckAmount($stockCheckAmount)
    {
        $this->stockCheckAmount = $stockCheckAmount;
    }

    /**
     * @return mixed
     */
    public function getStockCheckUser()
    {
        return $this->stockCheckUser;
    }

    /**
     * @param mixed $stockCheckUser
     */
    public function setStockCheckUser($stockCheckUser)
    {
        $this->stockCheckUser = $stockCheckUser;
    }

    /**
     * @return mixed
     */
    public function getStockCheckInfo()
    {
        return $this->stockCheckInfo;
    }

    /**
     * @param mixed $stockCheckInfo
     */
    public function setStockCheckInfo($stockCheckInfo)
    {
        $this->stockCheckInfo = $stockCheckInfo;
    }

    /**
     * @return mixed
     */
    public function getStockCheckTime()
    {
        return $this->stockCheckTime;
    }

    /**
     * @param mixed $stockCheckTime
     */
    public function setStockCheckTime($stockCheckTime)
    {
        $this->stockCheckTime = $stockCheckTime;
    }

    /**
     * @return mixed
     */
    public function getStockCheckState()
    {
        return $this->stockCheckState;
    }

    /**
     * @param mixed $stockCheckState
     */
    public function setStockCheckState($stockCheckState)
    {
        $this->stockCheckState = $stockCheckState;
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