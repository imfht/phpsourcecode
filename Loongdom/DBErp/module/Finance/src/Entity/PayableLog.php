<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Finance\Entity;

use Admin\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * 付款记录
 * @package Finance\Entity
 * @ORM\Entity(repositoryClass="Finance\Repository\PayableLogRepository")
 * @ORM\Table(name="dberp_finance_payable_log")
 */
class PayableLog extends BaseEntity
{
    /**
     * 付款记录id
     * @ORM\Id()
     * @ORM\Column(name="pay_log_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $payLogId;

    /**
     * 付款订单id
     * @ORM\Column(name="payable_id", type="integer", length=11)
     */
    private $payableId;

    /**
     * 付款金额
     * @ORM\Column(name="pay_log_amount", type="decimal", scale=4)
     */
    private $payLogAmount;

    /**
     * 付款人
     * @ORM\Column(name="pay_log_user", type="string", length=100)
     */
    private $payLogUser;

    /**
     * 付款时间
     * @ORM\Column(name="pay_log_paytime", type="integer", length=10)
     */
    private $payLogPaytime;

    /**
     * 付款凭证文件
     * @ORM\Column(name="pay_file", type="string", length=255)
     */
    private $payFile;

    /**
     * 付款备注
     * @ORM\Column(name="pay_log_info", type="string", length=255)
     */
    private $payLogInfo;

    /**
     * 付款记录添加时间
     * @ORM\Column(name="pay_log_addtime", type="integer", length=10)
     */
    private $payLogAddtime;

    /**
     * 操作者id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * @var
     * @ORM\OneToOne(targetEntity="Admin\Entity\AdminUser")
     * @ORM\JoinColumn(name="admin_id", referencedColumnName="admin_id")
     */
    private $oneAdmin;

    /**
     * @return mixed
     */
    public function getPayLogId()
    {
        return $this->payLogId;
    }

    /**
     * @param mixed $payLogId
     */
    public function setPayLogId($payLogId)
    {
        $this->payLogId = $payLogId;
    }

    /**
     * @return mixed
     */
    public function getPayableId()
    {
        return $this->payableId;
    }

    /**
     * @param mixed $payableId
     */
    public function setPayableId($payableId)
    {
        $this->payableId = $payableId;
    }

    /**
     * @return mixed
     */
    public function getPayLogAmount()
    {
        return $this->payLogAmount;
    }

    /**
     * @param mixed $payLogAmount
     */
    public function setPayLogAmount($payLogAmount)
    {
        $this->payLogAmount = $payLogAmount;
    }

    /**
     * @return mixed
     */
    public function getPayLogUser()
    {
        return $this->payLogUser;
    }

    /**
     * @param mixed $payLogUser
     */
    public function setPayLogUser($payLogUser)
    {
        $this->payLogUser = $payLogUser;
    }

    /**
     * @return mixed
     */
    public function getPayLogPaytime()
    {
        return $this->payLogPaytime;
    }

    /**
     * @param mixed $payLogPaytime
     */
    public function setPayLogPaytime($payLogPaytime)
    {
        $this->payLogPaytime = $payLogPaytime;
    }

    /**
     * @return mixed
     */
    public function getPayFile()
    {
        return $this->payFile;
    }

    /**
     * @param mixed $payFile
     */
    public function setPayFile($payFile)
    {
        $this->payFile = $payFile;
    }

    /**
     * @return mixed
     */
    public function getPayLogInfo()
    {
        return $this->payLogInfo;
    }

    /**
     * @param mixed $payLogInfo
     */
    public function setPayLogInfo($payLogInfo)
    {
        $this->payLogInfo = $payLogInfo;
    }

    /**
     * @return mixed
     */
    public function getPayLogAddtime()
    {
        return $this->payLogAddtime;
    }

    /**
     * @param mixed $payLogAddtime
     */
    public function setPayLogAddtime($payLogAddtime)
    {
        $this->payLogAddtime = $payLogAddtime;
    }

    /**
     * @return mixed
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * @param mixed $adminId
     */
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
    }

    /**
     * @return mixed
     */
    public function getOneAdmin()
    {
        return $this->oneAdmin;
    }

    /**
     * @param mixed $oneAdmin
     */
    public function setOneAdmin($oneAdmin)
    {
        $this->oneAdmin = $oneAdmin;
    }

}