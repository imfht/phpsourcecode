<?php
namespace Jykj\Teamlist\Domain\Model;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Yong Hui <huiyong@ngoos.org>, Jykj
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
/***
 *
 * This file is part of the "团队列表" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Yong Hui <huiyong@ngoos.org>, Jykj
 *
 ***/
/**
 * Team
 */
class Team extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * name
     * 姓名
     * 
     * @var string
     */
    protected $name = '';

    /**
     * intro
     * 简介
     * 
     * @var string
     */
    protected $intro = '';

    /**
     * image
     * 图片
     * 
     * @var string
     */
    protected $image = '';

    /**
     * detail
     * 详情
     * 
     * @var string
     */
    protected $detail = '';

    /**
     * spare1
     * 备选1
     * 
     * @var string
     */
    protected $spare1 = '';

    /**
     * spare2
     * 备选2
     * 
     * @var string
     */
    protected $spare2 = '';

    /**
     * spare3
     * 备选3
     * 
     * @var string
     */
    protected $spare3 = '';

    /**
     * orders
     * 排序
     * 
     * @var int
     */
    protected $orders = '';

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
     * Returns the intro
     * 
     * @return string $intro
     */
    public function getIntro()
    {
        return $this->intro;
    }

    /**
     * Sets the intro
     * 
     * @param string $intro
     * @return void
     */
    public function setIntro($intro)
    {
        $this->intro = $intro;
    }

    /**
     * Returns the image
     * 
     * @return string $image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the image
     * 
     * @param string $image
     * @return void
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Returns the detail
     * 
     * @return string $detail
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Sets the detail
     * 
     * @param string $detail
     * @return void
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;
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
     * Returns the orders
     * 
     * @return int orders
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * set the orders
     * 
     * @param int $orders
     * @return void
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    /**
     * 最后修改时间
     * 
     * @var \DateTime
     */
    protected $tstamp;
    
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
