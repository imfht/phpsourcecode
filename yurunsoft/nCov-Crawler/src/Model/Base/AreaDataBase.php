<?php
namespace ImiApp\Model\Base;

use Imi\Model\Model;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;

/**
 * AreaDataBase
 * @Entity
 * @Table(name="tb_area_data", id={"id", "modify_time"})
 * @property int $id 丁香园方ID
 * @property int $createTime 创建时间
 * @property int $modifyTime 更新时间
 * @property string $tags 标签
 * @property int $countryType 国家类型
 * @property int $provinceId 省ID
 * @property string $provinceName 省名
 * @property string $provinceShortName 省短名
 * @property string $cityName 城市名称
 * @property int $confirmedCount 确诊数量
 * @property int $suspectedCount 疑似数量
 * @property int $curedCount 治愈数量
 * @property int $deadCount 死亡数量
 * @property string $comment 注释
 * @property int $sort 排序
 */
abstract class AreaDataBase extends Model
{
    /**
     * 丁香园方ID
     * id
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=false)
     * @var int
     */
    protected $id;

    /**
     * 获取 id - 丁香园方ID
     *
     * @return int
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * 赋值 id - 丁香园方ID
     * @param int $id id
     * @return static
     */ 
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * 创建时间
     * create_time
     * @Column(name="create_time", type="bigint", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var int
     */
    protected $createTime;

    /**
     * 获取 createTime - 创建时间
     *
     * @return int
     */ 
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * 赋值 createTime - 创建时间
     * @param int $createTime create_time
     * @return static
     */ 
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
        return $this;
    }

    /**
     * 更新时间
     * modify_time
     * @Column(name="modify_time", type="bigint", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=false)
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

    /**
     * 标签
     * tags
     * @Column(name="tags", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $tags;

    /**
     * 获取 tags - 标签
     *
     * @return string
     */ 
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * 赋值 tags - 标签
     * @param string $tags tags
     * @return static
     */ 
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * 国家类型
     * country_type
     * @Column(name="country_type", type="tinyint", length=3, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var int
     */
    protected $countryType;

    /**
     * 获取 countryType - 国家类型
     *
     * @return int
     */ 
    public function getCountryType()
    {
        return $this->countryType;
    }

    /**
     * 赋值 countryType - 国家类型
     * @param int $countryType country_type
     * @return static
     */ 
    public function setCountryType($countryType)
    {
        $this->countryType = $countryType;
        return $this;
    }

    /**
     * 省ID
     * province_id
     * @Column(name="province_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var int
     */
    protected $provinceId;

    /**
     * 获取 provinceId - 省ID
     *
     * @return int
     */ 
    public function getProvinceId()
    {
        return $this->provinceId;
    }

    /**
     * 赋值 provinceId - 省ID
     * @param int $provinceId province_id
     * @return static
     */ 
    public function setProvinceId($provinceId)
    {
        $this->provinceId = $provinceId;
        return $this;
    }

    /**
     * 省名
     * province_name
     * @Column(name="province_name", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $provinceName;

    /**
     * 获取 provinceName - 省名
     *
     * @return string
     */ 
    public function getProvinceName()
    {
        return $this->provinceName;
    }

    /**
     * 赋值 provinceName - 省名
     * @param string $provinceName province_name
     * @return static
     */ 
    public function setProvinceName($provinceName)
    {
        $this->provinceName = $provinceName;
        return $this;
    }

    /**
     * 省短名
     * province_short_name
     * @Column(name="province_short_name", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $provinceShortName;

    /**
     * 获取 provinceShortName - 省短名
     *
     * @return string
     */ 
    public function getProvinceShortName()
    {
        return $this->provinceShortName;
    }

    /**
     * 赋值 provinceShortName - 省短名
     * @param string $provinceShortName province_short_name
     * @return static
     */ 
    public function setProvinceShortName($provinceShortName)
    {
        $this->provinceShortName = $provinceShortName;
        return $this;
    }

    /**
     * 城市名称
     * city_name
     * @Column(name="city_name", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
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
     * 注释
     * comment
     * @Column(name="comment", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $comment;

    /**
     * 获取 comment - 注释
     *
     * @return string
     */ 
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * 赋值 comment - 注释
     * @param string $comment comment
     * @return static
     */ 
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * 排序
     * sort
     * @Column(name="sort", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var int
     */
    protected $sort;

    /**
     * 获取 sort - 排序
     *
     * @return int
     */ 
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * 赋值 sort - 排序
     * @param int $sort sort
     * @return static
     */ 
    public function setSort($sort)
    {
        $this->sort = $sort;
        return $this;
    }

}
