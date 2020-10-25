<?php
namespace ImiApp\Model\Base;

use Imi\Model\Model;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;

/**
 * CityDataBase
 * @Entity
 * @Table(name="tb_city_data", id={"parent_id", "city_name", "modify_time"})
 * @property int $parentId 丁香园方省ID
 * @property string $provinceName 所属省名称
 * @property string $cityName 城市名称
 * @property int $confirmedCount 确诊数量
 * @property int $suspectedCount 疑似数量
 * @property int $curedCount 治愈数量
 * @property int $deadCount 死亡数量
 * @property int $modifyTime 更新时间
 */
abstract class CityDataBase extends Model
{
    /**
     * 丁香园方省ID
     * parent_id
     * @Column(name="parent_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=false)
     * @var int
     */
    protected $parentId;

    /**
     * 获取 parentId - 丁香园方省ID
     *
     * @return int
     */ 
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * 赋值 parentId - 丁香园方省ID
     * @param int $parentId parent_id
     * @return static
     */ 
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }

    /**
     * 所属省名称
     * province_name
     * @Column(name="province_name", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $provinceName;

    /**
     * 获取 provinceName - 所属省名称
     *
     * @return string
     */ 
    public function getProvinceName()
    {
        return $this->provinceName;
    }

    /**
     * 赋值 provinceName - 所属省名称
     * @param string $provinceName province_name
     * @return static
     */ 
    public function setProvinceName($provinceName)
    {
        $this->provinceName = $provinceName;
        return $this;
    }

    /**
     * 城市名称
     * city_name
     * @Column(name="city_name", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=false)
     * @var string
     */
    protected $cityName;

    /**
     * 获取 cityName - 城市名称
     *
     * @return string
     */ 
    public function getCityName()
    {
        return $this->cityName;
    }

    /**
     * 赋值 cityName - 城市名称
     * @param string $cityName city_name
     * @return static
     */ 
    public function setCityName($cityName)
    {
        $this->cityName = $cityName;
        return $this;
    }

    /**
     * 确诊数量
     * confirmed_count
     * @Column(name="confirmed_count", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var int
     */
    protected $confirmedCount;

    /**
     * 获取 confirmedCount - 确诊数量
     *
     * @return int
     */ 
    public function getConfirmedCount()
    {
        return $this->confirmedCount;
    }

    /**
     * 赋值 confirmedCount - 确诊数量
     * @param int $confirmedCount confirmed_count
     * @return static
     */ 
    public function setConfirmedCount($confirmedCount)
    {
        $this->confirmedCount = $confirmedCount;
        return $this;
    }

    /**
     * 疑似数量
     * suspected_count
     * @Column(name="suspected_count", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var int
     */
    protected $suspectedCount;

    /**
     * 获取 suspectedCount - 疑似数量
     *
     * @return int
     */ 
    public function getSuspectedCount()
    {
        return $this->suspectedCount;
    }

    /**
     * 赋值 suspectedCount - 疑似数量
     * @param int $suspectedCount suspected_count
     * @return static
     */ 
    public function setSuspectedCount($suspectedCount)
    {
        $this->suspectedCount = $suspectedCount;
        return $this;
    }

    /**
     * 治愈数量
     * cured_count
     * @Column(name="cured_count", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var int
     */
    protected $curedCount;

    /**
     * 获取 curedCount - 治愈数量
     *
     * @return int
     */ 
    public function getCuredCount()
    {
        return $this->curedCount;
    }

    /**
     * 赋值 curedCount - 治愈数量
     * @param int $curedCount cured_count
     * @return static
     */ 
    public function setCuredCount($curedCount)
    {
        $this->curedCount = $curedCount;
        return $this;
    }

    /**
     * 死亡数量
     * dead_count
     * @Column(name="dead_count", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var int
     */
    protected $deadCount;

    /**
     * 获取 deadCount - 死亡数量
     *
     * @return int
     */ 
    public function getDeadCount()
    {
        return $this->deadCount;
    }

    /**
     * 赋值 deadCount - 死亡数量
     * @param int $deadCount dead_count
     * @return static
     */ 
    public function setDeadCount($deadCount)
    {
        $this->deadCount = $deadCount;
        return $this;
    }

    /**
     * 更新时间
     * modify_time
     * @Column(name="modify_time", type="bigint", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=2, isAutoIncrement=false)
     * @var int
     */
    protected $modifyTime;

    /**
     * 获取 modifyTime - 更新时间
     *
     * @return int
     */ 
    public function getModifyTime()
    {
        return $this->modifyTime;
    }

    /**
     * 赋值 modifyTime - 更新时间
     * @param int $modifyTime modify_time
     * @return static
     */ 
    public function setModifyTime($modifyTime)
    {
        $this->modifyTime = $modifyTime;
        return $this;
    }

}
