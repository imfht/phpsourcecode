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
 * 志愿者信息收集
 */
class Volunteer extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * 姓名
     * 
     * @var string
     */
    protected $name = '';

    /**
     * 生日
     * 
     * @var string
     */
    protected $birthday = '';

    /**
     * 邮箱
     * 
     * @var string
     */
    protected $email = '';

    /**
     * 联系电话
     * 
     * @var string
     */
    protected $telephone = '';

    /**
     * QQ号
     * 
     * @var string
     */
    protected $qqcode = '';

    /**
     * 微博号
     * 
     * @var string
     */
    protected $weibo = '';

    /**
     * 简介
     * 
     * @var string
     */
    protected $descritpion = '';

    /**
     * 是否有志愿者经验
     * 
     * @var int
     */
    protected $isexperience = 0;

    /**
     * 技能专长
     * 
     * @var string
     */
    protected $skill = '';

    /**
     * 职位
     * 
     * @var string
     */
    protected $duty = '';

    /**
     * 所在单位
     * 
     * @var string
     */
    protected $org = '';

    /**
     * 所在队伍(参加组织名称)
     * 
     * @var string
     */
    protected $ranks = '';

    /**
     * 微信号
     * 
     * @var string
     */
    protected $wechat = '';

    /**
     * 身份证号
     * 
     * @var string
     */
    protected $idcard = '';

    /**
     * 紧急联系人
     * 
     * @var string
     */
    protected $emcontact = '';

    /**
     * 紧急联系电话
     * 
     * @var string
     */
    protected $emtelephone = '';

    /**
     * 性别
     * 
     * @var \Jykj\Dicts\Domain\Model\Dictitem
     */
    protected $sex = null;

    /**
     * 省份
     * 
     * @var \Jykj\Dicts\Domain\Model\Area
     */
    protected $province = null;

    /**
     * 所在社区
     * 
     * @var \Jykj\Dicts\Domain\Model\Dictitem
     */
    protected $community = null;

    /**
     * 身份 政治面貌
     * 
     * @var \Jykj\Dicts\Domain\Model\Dictitem
     */
    protected $identity = null;

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
     * Returns the birthday
     * 
     * @return string $birthday
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Sets the birthday
     * 
     * @param string $birthday
     * @return void
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * Returns the email
     * 
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email
     * 
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the telephone
     * 
     * @return string $telephone
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Sets the telephone
     * 
     * @param string $telephone
     * @return void
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * Returns the qqcode
     * 
     * @return string $qqcode
     */
    public function getQqcode()
    {
        return $this->qqcode;
    }

    /**
     * Sets the qqcode
     * 
     * @param string $qqcode
     * @return void
     */
    public function setQqcode($qqcode)
    {
        $this->qqcode = $qqcode;
    }

    /**
     * Returns the weibo
     * 
     * @return string $weibo
     */
    public function getWeibo()
    {
        return $this->weibo;
    }

    /**
     * Sets the weibo
     * 
     * @param string $weibo
     * @return void
     */
    public function setWeibo($weibo)
    {
        $this->weibo = $weibo;
    }

    /**
     * Returns the descritpion
     * 
     * @return string $descritpion
     */
    public function getDescritpion()
    {
        return $this->descritpion;
    }

    /**
     * Sets the descritpion
     * 
     * @param string $descritpion
     * @return void
     */
    public function setDescritpion($descritpion)
    {
        $this->descritpion = $descritpion;
    }

    /**
     * Returns the isexperience
     * 
     * @return int $isexperience
     */
    public function getIsexperience()
    {
        return $this->isexperience;
    }

    /**
     * Sets the isexperience
     * 
     * @param int $isexperience
     * @return void
     */
    public function setIsexperience($isexperience)
    {
        $this->isexperience = $isexperience;
    }

    /**
     * Returns the skill
     * 
     * @return string $skill
     */
    public function getSkill()
    {
        return $this->skill;
    }

    /**
     * Sets the skill
     * 
     * @param string $skill
     * @return void
     */
    public function setSkill($skill)
    {
        $this->skill = $skill;
    }

    /**
     * Returns the duty
     * 
     * @return string $duty
     */
    public function getDuty()
    {
        return $this->duty;
    }

    /**
     * Sets the duty
     * 
     * @param string $duty
     * @return void
     */
    public function setDuty($duty)
    {
        $this->duty = $duty;
    }

    /**
     * Returns the org
     * 
     * @return string $org
     */
    public function getOrg()
    {
        return $this->org;
    }

    /**
     * Sets the org
     * 
     * @param string $org
     * @return void
     */
    public function setOrg($org)
    {
        $this->org = $org;
    }

    /**
     * Returns the ranks
     * 
     * @return string $ranks
     */
    public function getRanks()
    {
        return $this->ranks;
    }

    /**
     * Sets the ranks
     * 
     * @param string $ranks
     * @return void
     */
    public function setRanks($ranks)
    {
        $this->ranks = $ranks;
    }

    /**
     * Returns the wechat
     * 
     * @return string $wechat
     */
    public function getWechat()
    {
        return $this->wechat;
    }

    /**
     * Sets the wechat
     * 
     * @param string $wechat
     * @return void
     */
    public function setWechat($wechat)
    {
        $this->wechat = $wechat;
    }

    /**
     * Returns the idcard
     * 
     * @return string $idcard
     */
    public function getIdcard()
    {
        return $this->idcard;
    }

    /**
     * Sets the idcard
     * 
     * @param string $idcard
     * @return void
     */
    public function setIdcard($idcard)
    {
        $this->idcard = $idcard;
    }

    /**
     * Returns the emcontact
     * 
     * @return string $emcontact
     */
    public function getEmcontact()
    {
        return $this->emcontact;
    }

    /**
     * Sets the emcontact
     * 
     * @param string $emcontact
     * @return void
     */
    public function setEmcontact($emcontact)
    {
        $this->emcontact = $emcontact;
    }

    /**
     * Returns the emtelephone
     * 
     * @return string $emtelephone
     */
    public function getEmtelephone()
    {
        return $this->emtelephone;
    }

    /**
     * Sets the emtelephone
     * 
     * @param string $emtelephone
     * @return void
     */
    public function setEmtelephone($emtelephone)
    {
        $this->emtelephone = $emtelephone;
    }

    /**
     * Returns the sex
     * 
     * @return \Jykj\Dicts\Domain\Model\Dictitem $sex
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Sets the sex
     * 
     * @param \Jykj\Dicts\Domain\Model\Dictitem $sex
     * @return void
     */
    public function setSex(\Jykj\Dicts\Domain\Model\Dictitem $sex)
    {
        $this->sex = $sex;
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
     * Returns the community
     * 
     * @return \Jykj\Dicts\Domain\Model\Dictitem $community
     */
    public function getCommunity()
    {
        return $this->community;
    }

    /**
     * Sets the community
     * 
     * @param \Jykj\Dicts\Domain\Model\Dictitem $community
     * @return void
     */
    public function setCommunity(\Jykj\Dicts\Domain\Model\Dictitem $community)
    {
        $this->community = $community;
    }

    /**
     * Returns the identity
     * 
     * @return \Jykj\Dicts\Domain\Model\Dictitem $identity
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * Sets the identity
     * 
     * @param \Jykj\Dicts\Domain\Model\Dictitem $identity
     * @return void
     */
    public function setIdentity(\Jykj\Dicts\Domain\Model\Dictitem $identity)
    {
        $this->identity = $identity;
    }
}
