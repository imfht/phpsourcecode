<?php
namespace ImiApp\Model\Base;

use Imi\Model\Model;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;

/**
 * StatisticsBase
 * @Entity
 * @Table(name="tb_statistics", id={"modify_time"})
 * @property int $id 
 * @property int $createTime 创建时间
 * @property int $modifyTime 更新时间
 * @property string $infectSource 传染源
 * @property string $passWay 传播途径
 * @property string $imgUrl 全国分布图
 * @property string $dailyPic 日走势图
 * @property string $summary 摘要
 * @property string $countRemark 统计备注
 * @property int $confirmedCount 确诊数量
 * @property int $suspectedCount 疑似数量
 * @property int $curedCount 治愈数量
 * @property int $deadCount 死亡数量
 * @property string $virus 病毒名称
 * @property string $remark1 备注1
 * @property string $remark2 备注2
 * @property string $remark3 备注3
 * @property string $remark4 备注4
 * @property string $remark5 备注5
 * @property string $generalRemark 一般说明
 * @property string $abroadRemark 国外备注
 */
abstract class StatisticsBase extends Model
{
    /**
     * id
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var int
     */
    protected $id;

    /**
     * 获取 id
     *
     * @return int
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * 赋值 id
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
     * @Column(name="modify_time", type="bigint", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=false)
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
     * 传染源
     * infect_source
     * @Column(name="infect_source", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $infectSource;

    /**
     * 获取 infectSource - 传染源
     *
     * @return string
     */ 
    public function getInfectSource()
    {
        return $this->infectSource;
    }

    /**
     * 赋值 infectSource - 传染源
     * @param string $infectSource infect_source
     * @return static
     */ 
    public function setInfectSource($infectSource)
    {
        $this->infectSource = $infectSource;
        return $this;
    }

    /**
     * 传播途径
     * pass_way
     * @Column(name="pass_way", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $passWay;

    /**
     * 获取 passWay - 传播途径
     *
     * @return string
     */ 
    public function getPassWay()
    {
        return $this->passWay;
    }

    /**
     * 赋值 passWay - 传播途径
     * @param string $passWay pass_way
     * @return static
     */ 
    public function setPassWay($passWay)
    {
        $this->passWay = $passWay;
        return $this;
    }

    /**
     * 全国分布图
     * img_url
     * @Column(name="img_url", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $imgUrl;

    /**
     * 获取 imgUrl - 全国分布图
     *
     * @return string
     */ 
    public function getImgUrl()
    {
        return $this->imgUrl;
    }

    /**
     * 赋值 imgUrl - 全国分布图
     * @param string $imgUrl img_url
     * @return static
     */ 
    public function setImgUrl($imgUrl)
    {
        $this->imgUrl = $imgUrl;
        return $this;
    }

    /**
     * 日走势图
     * daily_pic
     * @Column(name="daily_pic", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $dailyPic;

    /**
     * 获取 dailyPic - 日走势图
     *
     * @return string
     */ 
    public function getDailyPic()
    {
        return $this->dailyPic;
    }

    /**
     * 赋值 dailyPic - 日走势图
     * @param string $dailyPic daily_pic
     * @return static
     */ 
    public function setDailyPic($dailyPic)
    {
        $this->dailyPic = $dailyPic;
        return $this;
    }

    /**
     * 摘要
     * summary
     * @Column(name="summary", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $summary;

    /**
     * 获取 summary - 摘要
     *
     * @return string
     */ 
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * 赋值 summary - 摘要
     * @param string $summary summary
     * @return static
     */ 
    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * 统计备注
     * count_remark
     * @Column(name="count_remark", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $countRemark;

    /**
     * 获取 countRemark - 统计备注
     *
     * @return string
     */ 
    public function getCountRemark()
    {
        return $this->countRemark;
    }

    /**
     * 赋值 countRemark - 统计备注
     * @param string $countRemark count_remark
     * @return static
     */ 
    public function setCountRemark($countRemark)
    {
        $this->countRemark = $countRemark;
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
     * 病毒名称
     * virus
     * @Column(name="virus", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $virus;

    /**
     * 获取 virus - 病毒名称
     *
     * @return string
     */ 
    public function getVirus()
    {
        return $this->virus;
    }

    /**
     * 赋值 virus - 病毒名称
     * @param string $virus virus
     * @return static
     */ 
    public function setVirus($virus)
    {
        $this->virus = $virus;
        return $this;
    }

    /**
     * 备注1
     * remark1
     * @Column(name="remark1", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $remark1;

    /**
     * 获取 remark1 - 备注1
     *
     * @return string
     */ 
    public function getRemark1()
    {
        return $this->remark1;
    }

    /**
     * 赋值 remark1 - 备注1
     * @param string $remark1 remark1
     * @return static
     */ 
    public function setRemark1($remark1)
    {
        $this->remark1 = $remark1;
        return $this;
    }

    /**
     * 备注2
     * remark2
     * @Column(name="remark2", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $remark2;

    /**
     * 获取 remark2 - 备注2
     *
     * @return string
     */ 
    public function getRemark2()
    {
        return $this->remark2;
    }

    /**
     * 赋值 remark2 - 备注2
     * @param string $remark2 remark2
     * @return static
     */ 
    public function setRemark2($remark2)
    {
        $this->remark2 = $remark2;
        return $this;
    }

    /**
     * 备注3
     * remark3
     * @Column(name="remark3", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $remark3;

    /**
     * 获取 remark3 - 备注3
     *
     * @return string
     */ 
    public function getRemark3()
    {
        return $this->remark3;
    }

    /**
     * 赋值 remark3 - 备注3
     * @param string $remark3 remark3
     * @return static
     */ 
    public function setRemark3($remark3)
    {
        $this->remark3 = $remark3;
        return $this;
    }

    /**
     * 备注4
     * remark4
     * @Column(name="remark4", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $remark4;

    /**
     * 获取 remark4 - 备注4
     *
     * @return string
     */ 
    public function getRemark4()
    {
        return $this->remark4;
    }

    /**
     * 赋值 remark4 - 备注4
     * @param string $remark4 remark4
     * @return static
     */ 
    public function setRemark4($remark4)
    {
        $this->remark4 = $remark4;
        return $this;
    }

    /**
     * 备注5
     * remark5
     * @Column(name="remark5", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $remark5;

    /**
     * 获取 remark5 - 备注5
     *
     * @return string
     */ 
    public function getRemark5()
    {
        return $this->remark5;
    }

    /**
     * 赋值 remark5 - 备注5
     * @param string $remark5 remark5
     * @return static
     */ 
    public function setRemark5($remark5)
    {
        $this->remark5 = $remark5;
        return $this;
    }

    /**
     * 一般说明
     * general_remark
     * @Column(name="general_remark", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $generalRemark;

    /**
     * 获取 generalRemark - 一般说明
     *
     * @return string
     */ 
    public function getGeneralRemark()
    {
        return $this->generalRemark;
    }

    /**
     * 赋值 generalRemark - 一般说明
     * @param string $generalRemark general_remark
     * @return static
     */ 
    public function setGeneralRemark($generalRemark)
    {
        $this->generalRemark = $generalRemark;
        return $this;
    }

    /**
     * 国外备注
     * abroad_remark
     * @Column(name="abroad_remark", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $abroadRemark;

    /**
     * 获取 abroadRemark - 国外备注
     *
     * @return string
     */ 
    public function getAbroadRemark()
    {
        return $this->abroadRemark;
    }

    /**
     * 赋值 abroadRemark - 国外备注
     * @param string $abroadRemark abroad_remark
     * @return static
     */ 
    public function setAbroadRemark($abroadRemark)
    {
        $this->abroadRemark = $abroadRemark;
        return $this;
    }

}
