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
 * 志愿者报名表
 */
class Signup extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * 报名时间
     * 
     * @var int
     */
    protected $signtime = 0;

    /**
     * 签到时间
     * 
     * @var int
     */
    protected $checktime = 0;

    /**
     * 是否参加：0未参加 1参加
     * 
     * @var int
     */
    protected $status = 0;

    /**
     * 参与的活动
     * 
     * @var \Jykj\Activity\Domain\Model\Activity
     */
    protected $activityuid = null;

    /**
     * 志愿者
     * 
     * @var \Jykj\Activity\Domain\Model\Volunteer
     */
    protected $volunteer = null;

    /**
     * Returns the signtime
     * 
     * @return int $signtime
     */
    public function getSigntime()
    {
        return $this->signtime;
    }

    /**
     * Sets the signtime
     * 
     * @param int $signtime
     * @return void
     */
    public function setSigntime($signtime)
    {
        $this->signtime = $signtime;
    }

    /**
     * Returns the checktime
     * 
     * @return int $checktime
     */
    public function getChecktime()
    {
        return $this->checktime;
    }

    /**
     * Sets the checktime
     * 
     * @param int $checktime
     * @return void
     */
    public function setChecktime($checktime)
    {
        $this->checktime = $checktime;
    }

    /**
     * Returns the status
     * 
     * @return int $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the status
     * 
     * @param int $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Returns the activityuid
     * 
     * @return \Jykj\Activity\Domain\Model\Activity $activityuid
     */
    public function getActivityuid()
    {
        return $this->activityuid;
    }

    /**
     * Sets the activityuid
     * 
     * @param \Jykj\Activity\Domain\Model\Activity $activityuid
     * @return void
     */
    public function setActivityuid(\Jykj\Activity\Domain\Model\Activity $activityuid)
    {
        $this->activityuid = $activityuid;
    }

    /**
     * Returns the volunteer
     * 
     * @return \Jykj\Activity\Domain\Model\Volunteer $volunteer
     */
    public function getVolunteer()
    {
        return $this->volunteer;
    }

    /**
     * Sets the volunteer
     * 
     * @param \Jykj\Activity\Domain\Model\Volunteer $volunteer
     * @return void
     */
    public function setVolunteer(\Jykj\Activity\Domain\Model\Volunteer $volunteer)
    {
        $this->volunteer = $volunteer;
    }
}
