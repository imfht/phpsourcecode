<?php
namespace Jykj\Invoice\Domain\Model;


/***
 *
 * This file is part of the "发票管理" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Shichang Yang <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * 发票管理
 */
class Invoice extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * 捐赠金额
     * 
     * @var float
     */
    protected $money = 0.0;

    /**
     * 发票抬头
     * 
     * @var string
     */
    protected $header = '';

    /**
     * 邮寄地址
     * 
     * @var string
     */
    protected $address = '';

    /**
     * 邮政编码
     * 
     * @var string
     */
    protected $postcode = '';

    /**
     * 联系人
     * 
     * @var string
     */
    protected $people = '';

    /**
     * 联系电话
     * 
     * @var string
     */
    protected $telphone = '';

    /**
     * 邮件
     * 
     * @var string
     */
    protected $mail = '';

    /**
     * 捐款时间
     * 
     * @var \DateTime
     */
    protected $donatetime = null;

    /**
     * 税号
     * 
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
     * spare4
     * 
     * @var string
     */
    protected $spare4 = '';

    /**
     * spare5
     * 
     * @var string
     */
    protected $spare5 = '';

    /**
     * channelid
     * 
     * @var \Jykj\Invoice\Domain\Model\Channels
     */
    protected $channelid = null;

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
     * Returns the header
     * 
     * @return string $header
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Sets the header
     * 
     * @param string $header
     * @return void
     */
    public function setHeader($header)
    {
        $this->header = $header;
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
     * Returns the postcode
     * 
     * @return string $postcode
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Sets the postcode
     * 
     * @param string $postcode
     * @return void
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    /**
     * Returns the people
     * 
     * @return string $people
     */
    public function getPeople()
    {
        return $this->people;
    }

    /**
     * Sets the people
     * 
     * @param string $people
     * @return void
     */
    public function setPeople($people)
    {
        $this->people = $people;
    }

    /**
     * Returns the telphone
     * 
     * @return string $telphone
     */
    public function getTelphone()
    {
        return $this->telphone;
    }

    /**
     * Sets the telphone
     * 
     * @param string $telphone
     * @return void
     */
    public function setTelphone($telphone)
    {
        $this->telphone = $telphone;
    }

    /**
     * Returns the mail
     * 
     * @return string $mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Sets the mail
     * 
     * @param string $mail
     * @return void
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * Returns the donatetime
     * 
     * @return \DateTime $donatetime
     */
    public function getDonatetime()
    {
        return $this->donatetime;
    }

    /**
     * Sets the donatetime
     * 
     * @param \DateTime $donatetime
     * @return void
     */
    public function setDonatetime(\DateTime $donatetime)
    {
        $this->donatetime = $donatetime;
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
     * Returns the spare4
     * 
     * @return string $spare4
     */
    public function getSpare4()
    {
        return $this->spare4;
    }

    /**
     * Sets the spare4
     * 
     * @param string $spare4
     * @return void
     */
    public function setSpare4($spare4)
    {
        $this->spare4 = $spare4;
    }

    /**
     * Returns the spare5
     * 
     * @return string $spare5
     */
    public function getSpare5()
    {
        return $this->spare5;
    }

    /**
     * Sets the spare5
     * 
     * @param string $spare5
     * @return void
     */
    public function setSpare5($spare5)
    {
        $this->spare5 = $spare5;
    }

    /**
     * Returns the channelid
     * 
     * @return \Jykj\Invoice\Domain\Model\Channels $channelid
     */
    public function getChannelid()
    {
        return $this->channelid;
    }

    /**
     * Sets the channelid
     * 
     * @param \Jykj\Invoice\Domain\Model\Channels $channelid
     * @return void
     */
    public function setChannelid(\Jykj\Invoice\Domain\Model\Channels $channelid)
    {
        $this->channelid = $channelid;
    }
}
