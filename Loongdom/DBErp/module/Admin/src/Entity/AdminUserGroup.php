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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * 管理员组
 * @package Admin\Entity
 * @ORM\Entity(repositoryClass="Admin\Repository\AdminUserGroupRepository")
 * @ORM\Table(name="dberp_admin_group")
 */
class AdminUserGroup extends BaseEntity
{
    /**
     * 管理员组id
     * @ORM\Id()
     * @ORM\Column(name="admin_group_id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $adminGroupId;

    /**
     * 管理员组名称
     * @ORM\Column(name="admin_group_name")
     */
    private $adminGroupName;

    /**
     * 管理员组权限
     * @ORM\Column(name="admin_group_purview")
     */
    private $adminGroupPurview;

    /**
     * @ORM\OneToMany(targetEntity="Admin\Entity\AdminUser", mappedBy="group")
     * @ORM\JoinColumn(name="admin_group_id", referencedColumnName="admin_group_id")
     * @var
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function addUsers($user)
    {
        $this->users[] = $user;
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
    public function getAdminGroupName()
    {
        return $this->adminGroupName;
    }

    /**
     * @param mixed $adminGroupName
     */
    public function setAdminGroupName($adminGroupName)
    {
        $this->adminGroupName = $adminGroupName;
    }

    /**
     * @return mixed
     */
    public function getAdminGroupPurview()
    {
        return $this->adminGroupPurview;
    }

    /**
     * @param mixed $adminGroupPurview
     */
    public function setAdminGroupPurview($adminGroupPurview)
    {
        $this->adminGroupPurview = $adminGroupPurview;
    }
}