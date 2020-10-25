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

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Region
 * @package Admin\Entity
 * @ORM\Entity(repositoryClass="Admin\Repository\RegionRepository")
 * @ORM\Table(name="dberp_region")
 */
class Region extends BaseEntity
{
    /**
     * 地区id
     * @ORM\Id()
     * @ORM\Column(name="region_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $regionId;

    /**
     * 地区名称
     * @ORM\Column(name="region_name", type="string", length=50)
     */
    private $regionName;

    /**
     * 地区上一级id
     * @ORM\Column(name="region_top_id", type="integer", length=11)
     */
    private $regionTopId;

    /**
     * 地区排序
     * @ORM\Column(name="region_sort", type="integer", length=11)
     */
    private $regionSort;

    /**
     * 地区步长
     * @ORM\Column(name="region_path", type="string", length=100)
     */
    private $regionPath;

    /**
     * @return mixed
     */
    public function getRegionId()
    {
        return $this->regionId;
    }

    /**
     * @param mixed $regionId
     */
    public function setRegionId($regionId)
    {
        $this->regionId = $regionId;
    }

    /**
     * @return mixed
     */
    public function getRegionName()
    {
        return $this->regionName;
    }

    /**
     * @param mixed $regionName
     */
    public function setRegionName($regionName)
    {
        $this->regionName = $regionName;
    }

    /**
     * @return mixed
     */
    public function getRegionTopId()
    {
        return $this->regionTopId;
    }

    /**
     * @param mixed $regionTopId
     */
    public function setRegionTopId($regionTopId)
    {
        $this->regionTopId = $regionTopId;
    }

    /**
     * @return mixed
     */
    public function getRegionSort()
    {
        return $this->regionSort;
    }

    /**
     * @param mixed $regionSort
     */
    public function setRegionSort($regionSort)
    {
        $this->regionSort = $regionSort;
    }

    /**
     * @return mixed
     */
    public function getRegionPath()
    {
        return $this->regionPath;
    }

    /**
     * @param mixed $regionPath
     */
    public function setRegionPath($regionPath)
    {
        $this->regionPath = $regionPath;
    }

}