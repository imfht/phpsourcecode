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
 * Class Unit
 * @package Store\Entity
 * @ORM\Entity(repositoryClass="Store\Repository\UnitRepository")
 * @ORM\Table(name="dberp_unit")
 */
class Unit extends BaseEntity
{
    /**
     * 单位id
     * @ORM\Id()
     * @ORM\Column(name="unit_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $unitId;

    /**
     * 单位名称
     * @ORM\Column(name="unit_name", type="string", length=50)
     */
    private $unitName;

    /**
     * 单位排序
     * @ORM\Column(name="unit_sort", type="integer", length=11)
     */
    private $unitSort;

    /**
     * 管理员id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * @return mixed
     */
    public function getUnitId()
    {
        return $this->unitId;
    }

    /**
     * @param mixed $unitId
     */
    public function setUnitId($unitId)
    {
        $this->unitId = $unitId;
    }

    /**
     * @return mixed
     */
    public function getUnitName()
    {
        return $this->unitName;
    }

    /**
     * @param mixed $unitName
     */
    public function setUnitName($unitName)
    {
        $this->unitName = $unitName;
    }

    /**
     * @return mixed
     */
    public function getUnitSort()
    {
        return $this->unitSort;
    }

    /**
     * @param mixed $unitSort
     */
    public function setUnitSort($unitSort)
    {
        $this->unitSort = $unitSort;
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