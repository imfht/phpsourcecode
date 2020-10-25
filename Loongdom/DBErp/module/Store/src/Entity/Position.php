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
use Doctrine\ORM\Mapping as ORM;

/**
 * 仓位
 * Class Position
 * @package Store\Entity
 * @ORM\Entity(repositoryClass="Store\Repository\PositionRepository")
 * @ORM\Table(name="dberp_position")
 */
class Position extends BaseEntity
{
    /**
     * 仓位id
     * @ORM\Id()
     * @ORM\Column(
     *     name="position_id",
     *     type="integer",
     *     length=11
     *     )
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $positionId;

    /**
     * 仓位号
     * @ORM\Column(name="position_sn", type="string", length=30)
     */
    private $positionSn;

    /**
     * 仓库id
     * @ORM\Column(name="warehouse_id", type="integer", length=11)
     */
    private $warehouseId;

    /**
     * 管理员id（创建者）
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * 仓库
     * @ORM\ManyToOne(targetEntity="Store\Entity\Warehouse", inversedBy="positions")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="warehouse_id")
     */
    private $warehouse;

    /**
     * @return mixed
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * @param mixed $warehouse
     */
    public function setWarehouse(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
        $warehouse->addPositions($this);
    }

    /**
     * @return mixed
     */
    public function getPositionId()
    {
        return $this->positionId;
    }

    /**
     * @param mixed $positionId
     */
    public function setPositionId($positionId)
    {
        $this->positionId = $positionId;
    }

    /**
     * @return mixed
     */
    public function getPositionSn()
    {
        return $this->positionSn;
    }

    /**
     * @param mixed $positionSn
     */
    public function setPositionSn($positionSn)
    {
        $this->positionSn = $positionSn;
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