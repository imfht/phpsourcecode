<?php
namespace Jykj\Timeline\Domain\Model;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 WHB <wanghonbin@ngoos.org>, 极益科技
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * 大事记
 */
class Timeline extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * title
     *
     * 事件名称
     * @var string
     */
    protected $title = '';
    
    /**
     * eventdate
     *
     * 事件发生事件
     * @var \DateTime
     */
    protected $eventdate = null;
    
    /**
     * bodytext
     *
     * 大事件内容
     * @var string
     */
    protected $bodytext = '';
    
    /**
     * spare1
     *
     * 备用1
     * @var string
     */
    protected $spare1 = '';
    
    /**
     * spare2
     *
     * @var string
     */
    protected $spare2 = '';
    
    /**
     * spare3
     *
     * @var string
     */
    protected $spare3 = '';
    
    /**
     * crdate
     * 操作时间
     * @var \DateTime
     */
    protected $crdate = null;

    /**
     * 最后修改时间
     * 
     * @var \DateTime
     */
    protected $tstamp;

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * Returns the eventdate
     *
     * @return \DateTime $eventdate
     */
    public function getEventdate()
    {
        return $this->eventdate;
    }
    
    /**
     * Sets the eventdate
     *
     * @param \DateTime $eventdate
     * @return void
     */
    public function setEventdate(\DateTime $eventdate)
    {
        $this->eventdate = $eventdate;
    }
    
    /**
     * Returns the bodytext
     *
     * @return string $bodytext
     */
    public function getBodytext()
    {
        return $this->bodytext;
    }
    
    /**
     * Sets the bodytext
     *
     * @param string $bodytext
     * @return void
     */
    public function setBodytext($bodytext)
    {
        $this->bodytext = $bodytext;
    }
    
    /**
     * Returns the spare1
     *
     * @return string $spare1
     */
    public function getSpare1()
    {
        return $this->spare1;
    }
    
    /**
     * Sets the spare1
     *
     * @param string $spare1
     * @return void
     */
    public function setSpare1($spare1)
    {
        $this->spare1 = $spare1;
    }
    
    /**
     * Returns the spare2
     *
     * @return string $spare2
     */
    public function getSpare2()
    {
        return $this->spare2;
    }
    
    /**
     * Sets the spare2
     *
     * @param string $spare2
     * @return void
     */
    public function setSpare2($spare2)
    {
        $this->spare2 = $spare2;
    }
    
    /**
     * Returns the spare3
     *
     * @return string $spare3
     */
    public function getSpare3()
    {
        return $this->spare3;
    }
    
    /**
     * Sets the spare3
     *
     * @param string $spare3
     * @return void
     */
    public function setSpare3($spare3)
    {
        $this->spare3 = $spare3;
    }

    /**
     * Returns the crdate
     *
     * @return \DateTime $crdate
     */
    public function getCrdate()
    {
        return $this->crdate;
    }
    
    
    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTstamp()
    {
        return $this->tstamp;
    }
    
}