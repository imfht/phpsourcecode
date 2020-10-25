<?php
namespace Jykj\Donation\Domain\Model;


/***
 *
 * This file is part of the "Payment Module" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 王宏彬 <wanghongbin@ngoos.org>, 宁夏极益科技邮箱公司
 *
 ***/
/**
 * 支付,捐赠插件
 */
class Pay extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var boolean
     */
    protected $hidden;

    /**
     * @var integer
     */
    protected $crdate;

    /**
     * 捐赠留言
     * 
     * @var string
     */
    protected $comment = '';

    /**
     * 页面标题
     * 
     * @var string
     */
    protected $title = '';

    /**
     * 捐赠金额
     * 
     * @var float
     */
    protected $money = 0.0;

    /**
     * 捐赠人名称
     * 
     * @var string
     */
    protected $name = '';

    /**
     * 捐赠人邮箱
     * 
     * @var string
     */
    protected $email = '';

    /**
     * 捐赠人电话
     * 
     * @var string
     */
    protected $telephone = '';

    /**
     * 付款所属模块
     * 
     * @var string
     */
    protected $module = '';

    /**
     * 付款所在模块的数据ID
     * 
     * @var int
     */
    protected $datauid = 0;

    /**
     * 付款页面的链接
     * 
     * @var string
     */
    protected $url = '';

    /**
     * 付款平台返回的订单编号
     * 
     * @var string
     */
    protected $ordernumber = '';

    /**
     * 支付方式, 微信/支付宝
     * 
     * @var string
     */ 
    protected $payment = '';

    /**
     * 电子证书的编号
     * 
     * @var string
     */
    protected $certnumber = '';

    /**
     * 传播分享人的ID
     * 
     * @var int
     */
    protected $spreadshareuserid = 0;

    /**
     * Returns the comment
     * 
     * @return string $comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the comment
     * 
     * @param string $comment
     * @return void
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

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
     * Returns the module
     * 
     * @return string $module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Sets the module
     * 
     * @param string $module
     * @return void
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * Returns the datauid
     * 
     * @return int $datauid
     */
    public function getDatauid()
    {
        return $this->datauid;
    }

    /**
     * Sets the datauid
     * 
     * @param int $datauid
     * @return void
     */
    public function setDatauid($datauid)
    {
        $this->datauid = $datauid;
    }

    /**
     * Returns the url
     * 
     * @return string $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the url
     * 
     * @param string $url
     * @return void
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Returns the ordernumber
     * 
     * @return string $ordernumber
     */
    public function getOrdernumber()
    {
        return $this->ordernumber;
    }

    /**
     * Sets the ordernumber
     * 
     * @param string $ordernumber
     * @return void
     */
    public function setOrdernumber($ordernumber)
    {
        $this->ordernumber = $ordernumber;
    }

    /**
     * Returns the payment
     * 
     * @return string $payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Sets the payment
     * 
     * @param string $payment
     * @return void
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }

    /**
     * Returns the certnumber
     * 
     * @return string $certnumber
     */
    public function getCertnumber()
    {
        return $this->certnumber;
    }

    /**
     * Sets the certnumber
     * 
     * @param string $certnumber
     * @return void
     */
    public function setCertnumber($certnumber)
    {
        $this->certnumber = $certnumber;
    }

    /**
     * Returns the spreadshareuserid
     * 
     * @return int $spreadshareuserid
     */
    public function getSpreadshareuserid()
    {
        return $this->spreadshareuserid;
    }

    /**
     * Sets the spreadshareuserid
     * 
     * @param int $spreadshareuserid
     * @return void
     */
    public function setSpreadshareuserid($spreadshareuserid)
    {
        $this->spreadshareuserid = $spreadshareuserid;
    }

    /**
     * Set hidden flag
     *
     * @param integer $hidden hidden flag
     * @return void
     */
    public function setHidden($hidden) {
        $this->hidden = $hidden;
    }

    /**
     * Set creation date
     *
     * @return integer
     */
    public function setCrdate($crdate) {
        $this->crdate = $crdate;
    }

    /**
     * Get creation date
     *
     * @return integer
     */
    public function getCrdate() {
        return $this->crdate;
    }
}
