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

namespace Store\Entity;

use Admin\Entity\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * 仓库
 * Class Warehouse
 * @package Store\Entity
 * @ORM\Entity(repositoryClass="Store\Repository\WarehouseRepository")
 * @ORM\Table(name="dberp_warehouse")
 */
class Warehouse extends BaseEntity
{
    /**
     * 仓库id
     * @ORM\Id()
     * @ORM\Column(
     *     name="warehouse_id",
     *     type="integer",
     *     length=11
     *     )
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var
     */
    private $warehouseId;

    /**
     * 仓库编号
     * @ORM\Column(
     *     name="warehouse_sn",
     *     type="string",
     *     length=30
     *     )
     * @var
     */
    private $warehouseSn;

    /**
     * 仓库名称
     * @ORM\Column(
     *     name="warehouse_name",
     *     type="string",
     *     length=100
     *     )
     * @var
     */
    private $warehouseName;

    /**
     * 仓库管理员
     * @ORM\Column(name="warehouse_contacts", type="string", length=50)
     */
    private $warehouseContacts;

    /**
     * 联系电话
     * @ORM\Column(name="warehouse_phone", type="string", length=30)
     */
    private $warehousePhone;
    /**
     * 仓库排序
     * @ORM\Column(name="warehouse_sort", type="integer", length=11)
     */
    private $warehouseSort;

    /**
     * 管理员id，创建人id
     * @ORM\Column(
     *     name="admin_id",
     *     type="integer",
     *     length=11
     *     )
     * @var
     */
    private $adminId;

    /**
     * 连接仓位数据表
     * @ORM\OneToMany(targetEntity="Store\Entity\Position", mappedBy="warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="warehouse_id")
     */
    private $positions;

    public function __construct()
    {
        $this->positions = new ArrayCollection();
    }

    public function getPositions()
    {
        return $this->positions;
    }

    public function addPositions($position)
    {
        $this->positions[] = $position;
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
    public function getWarehouseSn()
    {
        return $this->warehouseSn;
    }

    /**
     * @param mixed $warehouseSn
     */
    public function setWarehouseSn($warehouseSn)
    {
        $this->warehouseSn = $warehouseSn;
    }

    /**
     * @return mixed
     */
    public function getWarehouseName()
    {
        return $this->warehouseName;
    }

    /**
     * @param mixed $warehouseName
     */
    public function setWarehouseName($warehouseName)
    {
        $this->warehouseName = $warehouseName;
    }

    /**
     * @return mixed
     */
    public function getWarehouseContacts()
    {
        return $this->warehouseContacts;
    }

    /**
     * @param mixed $warehouseContacts
     */
    public function setWarehouseContacts($warehouseContacts)
    {
        $this->warehouseContacts = $warehouseContacts;
    }

    /**
     * @return mixed
     */
    public function getWarehousePhone()
    {
        return $this->warehousePhone;
    }

    /**
     * @param mixed $warehousePhone
     */
    public function setWarehousePhone($warehousePhone)
    {
        $this->warehousePhone = $warehousePhone;
    }

    /**
     * @return mixed
     */
    public function getWarehouseSort()
    {
        return $this->warehouseSort;
    }

    /**
     * @param mixed $warehouseSort
     */
    public function setWarehouseSort($warehouseSort)
    {
        $this->warehouseSort = $warehouseSort;
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