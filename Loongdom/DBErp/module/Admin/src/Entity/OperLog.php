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

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 操作日志
 * Class OperLog
 * @package Admin\Entity
 * @ORM\Entity(repositoryClass="Admin\Repository\OperLogRepository")
 * @ORM\Table(name="dberp_operlog")
 */
class OperLog extends BaseEntity
{
    /**
     * 自增ip
     * @ORM\Id()
     * @ORM\Column(name="log_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $logId;

    /**
     * 操作者
     * @ORM\Column(name="log_oper_user", type="string", length=100)
     */
    private $logOperUser;

    /**
     * 操作所在会员组
     * @ORM\Column(name="log_oper_user_group", type="string", length=100)
     */
    private $logOperUserGroup;

    /**
     * 操作时间
     * @ORM\Column(name="log_time", type="integer", length=10)
     */
    private $logTime;

    /**
     * 操作者ip
     * @ORM\Column(name="log_ip", type="string", length=50)
     */
    private $logIp;

    /**
     * 操作内容
     * @ORM\Column(name="log_body", type="string", length=2000)
     */
    private $logBody;

    /**
     * @return mixed
     */
    public function getLogId()
    {
        return $this->logId;
    }

    /**
     * @param mixed $logId
     */
    public function setLogId($logId)
    {
        $this->logId = $logId;
    }

    /**
     * @return mixed
     */
    public function getLogOperUser()
    {
        return $this->logOperUser;
    }

    /**
     * @param mixed $logOperUser
     */
    public function setLogOperUser($logOperUser)
    {
        $this->logOperUser = $logOperUser;
    }

    /**
     * @return mixed
     */
    public function getLogOperUserGroup()
    {
        return $this->logOperUserGroup;
    }

    /**
     * @param mixed $logOperUserGroup
     */
    public function setLogOperUserGroup($logOperUserGroup)
    {
        $this->logOperUserGroup = $logOperUserGroup;
    }

    /**
     * @return mixed
     */
    public function getLogTime()
    {
        return $this->logTime;
    }

    /**
     * @param mixed $logTime
     */
    public function setLogTime($logTime)
    {
        $this->logTime = $logTime;
    }

    /**
     * @return mixed
     */
    public function getLogIp()
    {
        return $this->logIp;
    }

    /**
     * @param mixed $logIp
     */
    public function setLogIp($logIp)
    {
        $this->logIp = $logIp;
    }

    /**
     * @return mixed
     */
    public function getLogBody()
    {
        return $this->logBody;
    }

    /**
     * @param mixed $logBody
     */
    public function setLogBody($logBody)
    {
        $this->logBody = $logBody;
    }

}