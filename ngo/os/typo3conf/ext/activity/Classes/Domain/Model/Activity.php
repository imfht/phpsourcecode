<?php
namespace Jykj\Activity\Domain\Model;


/***
 *
 * This file is part of the "志愿者活动" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 yangshichang <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * 志愿者活动
 */
class Activity extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * 活动名称
     * 
     * @var string
     */
    protected $name = '';

    /**
     * 省份
     * 
     * @var \Jykj\Dicts\Domain\Model\Area
     */
    protected $province = null;

    /**
     * 市
     * 
     * @var  \Jykj\Dicts\Domain\Model\Area
     */
    protected $city = null;

    /**
     * 区
     * 
     * @var  \Jykj\Dicts\Domain\Model\Area
     */
    protected $area = null;

    /**
     * 地址
     * 
     * @var string
     */
    protected $address = '';

    /**
     * 所在商圈
     * 
     * @var \Jykj\Dicts\Domain\Model\Dictitem
     */
    protected $trad = null;

    /**
     * 活动类别
     * 
     * @var \Jykj\Dicts\Domain\Model\Dictitem
     */
    protected $types = null;

    /**
     * 活动标签
     * 
     * @var string
     */
    protected $tag = '';

    /**
     * 活动人数
     * 
     * @var int
     */
    protected $people = 0;

    /**
     * 活动海报
     * 
     * @var string
     */
    protected $pictures = '';

    /**
     * 活动开始时间
     * 
     * @var int
     */
    protected $sttime = 0;

    /**
     * 活动结束时间
     * 
     * @var int
     */
    protected $overtime = 0;

    /**
     * 活动简介
     * 
     * @var string
     */
    protected $introduce = '';

    /**
     * 活动详情
     * 
     * @var string
     */
    protected $contents = '';

    /**
     * 二维码地址
     * 
     * @var string
     */
    protected $qrcode = '';

    /**
     * 发布状态：0未发布；1发布；2下线
     * 
     * @var int
     */
    protected $sendstat = 0;

    /**
     * 是否收费：0免费 1收费
     * 
     * @var int
     */
    protected $mode = 0;

    /**
     * 缴费金额
     * 
     * @var float
     */
    protected $money = 0.0;

    /**
     * 审核状态：0待审核；1审核成功；2审核失败
     * 
     * @var int
     */
    protected $ckstat = 0;

    /**
     * 审核结果：不通过的时候填写
     * 
     * @var string
     */
    protected $results = '';

    /**
     * 审核人
     * 
     * @var int
     */
    protected $checkuser = 0;

    /**
     * 发布人
     * 
     * @var int
     */
    protected $senduser = 0;

      /**
     * 活动方式
     * 0 普通活动；1常态化活动
     * @var int
     */
    protected $way = 0;

     /**
     * 常态化活动 按周进行
     * 0 默认；1-7 表示周一到周天
     * @var int
     */
    protected $week = 0;

      /**
     * 常态化活动 填写时间
     * hh:mm
     * @var string
     */
    protected $hour = "";

    /**
     * 删除标记
     * 0正常 1 删除
     * @var int
     */
    protected $deltag=0;

     /**
     * 通过sql获得多个标签的名称
     *
     * @var string
     */
    protected $tagname="";

    /**
     * Returns the name
     * 
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     * 
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the province
     * 
     * @return \Jykj\Dicts\Domain\Model\Area $province
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Sets the province
     * 
     * @param \Jykj\Dicts\Domain\Model\Area $province
     * @return void
     */
    public function setProvince(\Jykj\Dicts\Domain\Model\Area $province)
    {
        $this->province = $province;
    }

    /**
     * Returns the city
     * 
     * @return \Jykj\Dicts\Domain\Model\Area $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the city
     * 
     * @param \Jykj\Dicts\Domain\Model\Area $city
     * @return void
     */
    public function setCity( \Jykj\Dicts\Domain\Model\Area $city)
    {
        $this->city = $city;
    }

    /**
     * Returns the area
     * 
     * @return \Jykj\Dicts\Domain\Model\Area $area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Sets the area
     * 
     * @param \Jykj\Dicts\Domain\Model\Area $area
     * @return void
     */
    public function setArea(\Jykj\Dicts\Domain\Model\Area $area)
    {
        $this->area = $area;
    }

    /**
     * Returns the address
     * 
     * @return string $address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Sets the address
     * 
     * @param string $address
     * @return void
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Returns the trad
     * 
     * @return \Jykj\Dicts\Domain\Model\Dictitem $trad
     */
    public function getTrad()
    {
        return $this->trad;
    }

    /**
     * Sets the trad
     * 
     * @param \Jykj\Dicts\Domain\Model\Dictitem $trad
     * @return void
     */
    public function setTrad(\Jykj\Dicts\Domain\Model\Dictitem $trad)
    {
        $this->trad = $trad;
    }

    /**
     * Returns the types
     * 
     * @return \Jykj\Dicts\Domain\Model\Dictitem $types
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Sets the types
     * 
     * @param \Jykj\Dicts\Domain\Model\Dictitem $types
     * @return void
     */
    public function setTypes(\Jykj\Dicts\Domain\Model\Dictitem $types)
    {
        $this->types = $types;
    }

    /**
     * Returns the tag
     * 
     * @return string $tag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Sets the tag
     * 
     * @param string $tag
     * @return void
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * Returns the people
     * 
     * @return int $people
     */
    public function getPeople()
    {
        return $this->people;
    }

    /**
     * Sets the people
     * 
     * @param int $people
     * @return void
     */
    public function setPeople($people)
    {
        $this->people = $people;
    }

    /**
     * Returns the pictures
     * 
     * @return string $pictures
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    /**
     * Sets the pictures
     * 
     * @param string $pictures
     * @return void
     */
    public function setPictures($pictures)
    {
        $this->pictures = $pictures;
    }

    /**
     * Returns the sttime
     * 
     * @return int $sttime
     */
    public function getSttime()
    {
        return $this->sttime;
    }

    /**
     * Sets the sttime
     * 
     * @param int $sttime
     * @return void
     */
    public function setSttime($sttime)
    {
        $this->sttime = $sttime;
    }

    /**
     * Returns the overtime
     * 
     * @return int $overtime
     */
    public function getOvertime()
    {
        return $this->overtime;
    }

    /**
     * Sets the overtime
     * 
     * @param int $overtime
     * @return void
     */
    public function setOvertime($overtime)
    {
        $this->overtime = $overtime;
    }

    /**
     * Returns the introduce
     * 
     * @return string $introduce
     */
    public function getIntroduce()
    {
        return $this->introduce;
    }

    /**
     * Sets the introduce
     * 
     * @param string $introduce
     * @return void
     */
    public function setIntroduce($introduce)
    {
        $this->introduce = $introduce;
    }

    /**
     * Returns the contents
     * 
     * @return string $contents
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Sets the contents
     * 
     * @param string $contents
     * @return void
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    /**
     * Returns the qrcode
     * 
     * @return string $qrcode
     */
    public function getQrcode()
    {
        return $this->qrcode;
    }

    /**
     * Sets the qrcode
     * 
     * @param string $qrcode
     * @return void
     */
    public function setQrcode($qrcode)
    {
        $this->qrcode = $qrcode;
    }

    /**
     * Returns the sendstat
     * 
     * @return int $sendstat
     */
    public function getSendstat()
    {
        return $this->sendstat;
    }

    /**
     * Sets the sendstat
     * 
     * @param int $sendstat
     * @return void
     */
    public function setSendstat($sendstat)
    {
        $this->sendstat = $sendstat;
    }

    /**
     * Returns the mode
     * 
     * @return int $mode
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Sets the mode
     * 
     * @param int $mode
     * @return void
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Returns the money
     * 
     * @return float $money
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * Sets the money
     * 
     * @param float $money
     * @return void
     */
    public function setMoney($money)
    {
        $this->money = $money;
    }

    /**
     * Returns the way
     *
     * @return int $way
     */
    public function getWay()
    {
        return $this->way;
    }
    
    /**
     * Sets the way
     *
     * @param int $way
     * @return void
     */
    public function setWay($way)
    {
        $this->way = $way;
    }

     /**
     * Returns the week
     *
     * @return int $week
     */
    public function getWeek()
    {
        return $this->week;
    }
    
    /**
     * Sets the week
     *
     * @param int $week
     * @return void
     */
    public function setWeek($week)
    {
        $this->week = $week;
    }

     /**
     * Returns the hour
     *
     * @return string $hour
     */
    public function getHour()
    {
        return $this->hour;
    }
    
    /**
     * Sets the hour
     *
     * @param int $hour
     * @return void
     */
    public function setHour($hour)
    {
        $this->hour = $hour;
    }


    /**
     * Returns the deltag
     *
     * @return int $deltag
     */
    public function getDeltag()
    {
        return $this->deltag;
    }
    
    /**
     * Sets the deltag
     *
     * @param int $deltag
     * @return void
     */
    public function setDeltag($deltag)
    {
        $this->deltag = $deltag;
    }

    /**
     * Returns the ckstat
     * 
     * @return int $ckstat
     */
    public function getCkstat()
    {
        return $this->ckstat;
    }

    /**
     * Sets the ckstat
     * 
     * @param int $ckstat
     * @return void
     */
    public function setCkstat($ckstat)
    {
        $this->ckstat = $ckstat;
    }

    /**
     * Returns the results
     * 
     * @return string $results
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Sets the results
     * 
     * @param string $results
     * @return void
     */
    public function setResults($results)
    {
        $this->results = $results;
    }

    /**
     * Returns the checkuser
     * 
     * @return int $checkuser
     */
    public function getCheckuser()
    {
        return $this->checkuser;
    }

    /**
     * Sets the checkuser
     * 
     * @param int $checkuser
     * @return void
     */
    public function setCheckuser($checkuser)
    {
        $this->checkuser = $checkuser;
    }

    /**
     * Returns the senduser
     * 
     * @return int $senduser
     */
    public function getSenduser()
    {
        return $this->senduser;
    }

    /**
     * Sets the senduser
     * 
     * @param int $senduser
     * @return void
     */
    public function setSenduser($senduser)
    {
        $this->senduser = $senduser;
    }

    /**
     * Returns the tagname
     * 
     * @return string $tagname
     */
    public function getTagname()
    {
        $queryBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)->getQueryBuilderForTable('tx_dicts_domain_model_dictitem');
        $dictlist = $queryBuilder
            ->select('*')
            ->from('tx_dicts_domain_model_dictitem')
            ->where(
                $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($this->tag)),
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0)),
                $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0))
            )
            ->execute()
            ->fetchAll();
        for ($i = 0; $i < count($dictlist); $i++) {
            $tagnames .= $dictlist[$i]["name"] . "  ";
        }
        $this->tagname = $tagnames;
        return $this->tagname;
    }
}
