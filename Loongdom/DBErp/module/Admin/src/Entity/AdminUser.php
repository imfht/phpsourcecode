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
use Zend\Crypt\Password\Bcrypt;

/**
 * 管理员
 * @package Admin\Entity
 * @ORM\Entity(repositoryClass="Admin\Repository\AdminUserRepository")
 * @ORM\Table(name="dberp_admin")
 */
class AdminUser extends BaseEntity
{
    /**
     * 管理员id
     * @ORM\Id()
     * @ORM\Column(name="admin_id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $adminId;

    /**
     * 管理员组id
     * @ORM\Column(
     *     name="admin_group_id",
     *     type="integer"
     * )
     */
    private $adminGroupId;

    /**
     * 管理员名称
     * @ORM\Column(
     *     name="admin_name",
     *     type="string",
     *     length=100
     *     )
     */
    private $adminName;

    /**
     * 管理员密码
     * @ORM\Column(
     *     name="admin_passwd",
     *     type="string",
     *     length=72
     *     )
     */
    private $adminPassword;

    /**
     * 管理员邮箱
     * @ORM\Column(
     *     name="admin_email",
     *     type="string",
     *     length=100
     *     )
     */
    private $adminEmail;

    /**
     * 管理员状态(0 禁用，1 启用)
     * @ORM\Column(name="admin_state", type="integer", length=2)
     */
    private $adminState;

    /**
     * 管理员添加时间
     * @ORM\Column(
     *     name="admin_add_time",
     *     type="integer"
     * )
     */
    private $adminAddTime;

    /**
     * 管理员登录时间（旧）
     * @ORM\Column(
     *     name="admin_old_login_time",
     *     type="integer"
     * )
     */
    private $adminOldLoginTime;

    /**
     * 管理员登录时间（新）
     * @ORM\Column(
     *     name="admin_new_login_time",
     *     type="integer",
     *     length=10
     * )
     */
    private $adminNewLoginTime;

    /**
     * @ORM\ManyToOne(targetEntity="Admin\Entity\AdminUserGroup", inversedBy="users")
     * @ORM\JoinColumn(name="admin_group_id", referencedColumnName="admin_group_id")
     *
     */
    private $group;

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup(AdminUserGroup $group)
    {
        $group->addUsers($this);
        $this->group = $group;
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
    public function getAdminGroupId()
    {
        return $this->adminGroupId;
    }

    /**
     * @param mixed $adminGroupId
     */
    public function setAdminGroupId($adminGroupId)
    {
        $this->adminGroupId = $adminGroupId;
    }

    /**
     * @return mixed
     */
    public function getAdminName()
    {
        return $this->adminName;
    }

    /**
     * @param mixed $adminName
     */
    public function setAdminName($adminName)
    {
        $this->adminName = $adminName;
    }

    /**
     * @return mixed
     */
    public function getAdminPassword()
    {
        return $this->adminPassword;
    }

    /**
     * @param mixed $adminPassword
     */
    public function setAdminPassword($adminPassword)
    {
        $this->adminPassword = $this->createAdminPassword($adminPassword);
    }

    /**
     * @return mixed
     */
    public function getAdminEmail()
    {
        return $this->adminEmail;
    }

    /**
     * @param mixed $adminEmail
     */
    public function setAdminEmail($adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }

    /**
     * @return mixed
     */
    public function getAdminState()
    {
        return $this->adminState;
    }

    /**
     * @param mixed $adminState
     */
    public function setAdminState($adminState)
    {
        $this->adminState = $adminState;
    }

    /**
     * @return mixed
     */
    public function getAdminAddTime()
    {
        return $this->adminAddTime;
    }

    /**
     * @param mixed $adminAddTime
     */
    public function setAdminAddTime($adminAddTime)
    {
        $this->adminAddTime = $adminAddTime;
    }

    /**
     * @return mixed
     */
    public function getAdminOldLoginTime()
    {
        return $this->adminOldLoginTime;
    }

    /**
     * @param mixed $adminOldLoginTime
     */
    public function setAdminOldLoginTime($adminOldLoginTime)
    {
        $this->adminOldLoginTime = $adminOldLoginTime;
    }

    /**
     * @return mixed
     */
    public function getAdminNewLoginTime()
    {
        return $this->adminNewLoginTime;
    }

    /**
     * @param mixed $adminNewLoginTime
     */
    public function setAdminNewLoginTime($adminNewLoginTime)
    {
        $this->adminNewLoginTime = $adminNewLoginTime;
    }

    /**
     * 生成密码
     * @param $password
     * @return string
     */
    private function createAdminPassword($password)
    {
        $bcPassword = new Bcrypt();
        return $bcPassword->create($password);
    }
}