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
 * 收款记录
 * Class ReceivableLog
 * @package Finance\Entity
 * @ORM\Entity(repositoryClass="Finance\Repository\ReceivableLogRepository")
 * @ORM\Table(name="dberp_accounts_receivable_log")
 */
class ReceivableLog extends BaseEntity
{
    /**
     * 自增id
     * @ORM\Id()
     * @ORM\Column(name="receivable_log_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $receivableLogId;

    /**
     * 收款订单id
     * @ORM\Column(name="receivable_id", type="integer", length=11)
     */
    private $receivableId;

    /**
     * 收款金额
     * @ORM\Column(name="receivable_log_amount", type="decimal", scale=4)
     */
    private $receivableLogAmount;

    /**
     * 收款人
     * @ORM\Column(name="receivable_log_user", type="string", length=100)
     */
    private $receivableLogUser;

    /**
     * 收款时间
     * @ORM\Column(name="receivable_log_time", type="integer", length=10)
     */
    private $receivableLogTime;

    /**
     * 收款凭证文件
     * @ORM\Column(name="receivable_file", type="string", length=255)
     */
    private $receivableFile;

    /**
     * 收款备注信息
     * @ORM\Column(name="receivable_info", type="string", length=255)
     */
    private $receivableInfo;

    /**
     * 收款记录添加时间
     * @ORM\Column(name="receivable_add_time", type="integer", length=10)
     */
    private $receivableAddTime;

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
    public function getReceivableLogId()
    {
        return $this->receivableLogId;
    }

    /**
     * @param mixed $receivableLogId
     */
    public function setReceivableLogId($receivableLogId)
    {
        $this->receivableLogId = $receivableLogId;
    }

    /**
     * @return mixed
     */
    public function getReceivableId()
    {
        return $this->receivableId;
    }

    /**
     * @param mixed $receivableId
     */
    public function setReceivableId($receivableId)
    {
        $this->receivableId = $receivableId;
    }

    /**
     * @return mixed
     */
    public function getReceivableLogAmount()
    {
        return $this->receivableLogAmount;
    }

    /**
     * @param mixed $receivableAmount
     */
    public function setReceivableLogAmount($receivableLogAmount)
    {
        $this->receivableLogAmount = $receivableLogAmount;
    }

    /**
     * @return mixed
     */
    public function getReceivableLogUser()
    {
        return $this->receivableLogUser;
    }

    /**
     * @param mixed $receivableLogUser
     */
    public function setReceivableLogUser($receivableLogUser)
    {
        $this->receivableLogUser = $receivableLogUser;
    }

    /**
     * @return mixed
     */
    public function getReceivableLogTime()
    {
        return $this->receivableLogTime;
    }

    /**
     * @param mixed $receivableLogTime
     */
    public function setReceivableLogTime($receivableLogTime)
    {
        $this->receivableLogTime = $receivableLogTime;
    }

    /**
     * @return mixed
     */
    public function getReceivableFile()
    {
        return $this->receivableFile;
    }

    /**
     * @param mixed $receivableFile
     */
    public function setReceivableFile($receivableFile)
    {
        $this->receivableFile = $receivableFile;
    }

    /**
     * @return mixed
     */
    public function getReceivableInfo()
    {
        return $this->receivableInfo;
    }

    /**
     * @param mixed $receivableInfo
     */
    public function setReceivableInfo($receivableInfo)
    {
        $this->receivableInfo = $receivableInfo;
    }

    /**
     * @return mixed
     */
    public function getReceivableAddTime()
    {
        return $this->receivableAddTime;
    }

    /**
     * @param mixed $receivableAddTime
     */
    public function setReceivableAddTime($receivableAddTime)
    {
        $this->receivableAddTime = $receivableAddTime;
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