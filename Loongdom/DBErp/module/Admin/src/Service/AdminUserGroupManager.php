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

namespace Admin\Service;

use Admin\Entity\AdminUser;
use Admin\Entity\AdminUserGroup;
use Doctrine\ORM\EntityManager;

class AdminUserGroupManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加管理员组
     * @param array $data
     * @return AdminUserGroup
     */
    public function addAdminGroup(array $data)
    {
        $adminGroup = new AdminUserGroup();
        $adminGroup->valuesSet($data);
        if(!empty($data['adminGroupPermission'])) {
            $adminGroup->setAdminGroupPurview(implode(',', $data['adminGroupPermission']));
        }

        $this->entityManager->persist($adminGroup);
        $this->entityManager->flush();

        return $adminGroup;
    }

    /**
     * 更新管理员组
     * @param $adminGroup
     * @param $data
     * @return bool
     */
    public function updateAdminGroup(AdminUserGroup $adminGroup, $data)
    {
        $adminGroup->valuesSet($data);
        if(!empty($data['adminGroupPermission'])) {
            $adminGroup->setAdminGroupPurview(implode(',', $data['adminGroupPermission']));
        } else $adminGroup->setAdminGroupPurview('');

        $this->entityManager->flush();

        return true;
    }
    /**
     * 删除管理员组
     * @param $adminGroup
     * @return bool
     */
    public function deleteAdminGroup($adminGroup)
    {
        $adminUser = $this->entityManager->getRepository(AdminUser::class)->findOneBy(['adminGroupId'=>$adminGroup->getAdminGroupId()]);
        if($adminUser == null) {
            $this->entityManager->remove($adminGroup);
            $this->entityManager->flush();
            return true;
        }
        return false;
    }
}