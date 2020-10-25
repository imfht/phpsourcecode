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
 * Class System
 * @package Admin\Entity
 * @ORM\Entity(repositoryClass="Admin\Repository\SystemRepository")
 * @ORM\Table(name="dberp_system")
 */
class System extends BaseEntity
{
    /**
     * 自增id
     * @ORM\Id()
     * @ORM\Column(name="sys_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sysId;

    /**
     * 系统设置标记
     * @ORM\Column(name="sys_name", type="string", length=30)
     */
    private $sysName;

    /**
     * 系统设置内容
     * @ORM\Column(name="sys_body", type="text")
     */
    private $sysBody;

    /**
     * 系统设置类型
     * @ORM\Column(name="sys_type", type="string", length=15)
     */
    private $sysType;

    /**
     * @return mixed
     */
    public function getSysId()
    {
        return $this->sysId;
    }

    /**
     * @param mixed $sysId
     */
    public function setSysId($sysId)
    {
        $this->sysId = $sysId;
    }

    /**
     * @return mixed
     */
    public function getSysName()
    {
        return $this->sysName;
    }

    /**
     * @param mixed $sysName
     */
    public function setSysName($sysName)
    {
        $this->sysName = $sysName;
    }

    /**
     * @return mixed
     */
    public function getSysBody()
    {
        return $this->sysBody;
    }

    /**
     * @param mixed $sysBody
     */
    public function setSysBody($sysBody)
    {
        $this->sysBody = $sysBody;
    }

    /**
     * @return mixed
     */
    public function getSysType()
    {
        return $this->sysType;
    }

    /**
     * @param mixed $sysType
     */
    public function setSysType($sysType)
    {
        $this->sysType = $sysType;
    }

}