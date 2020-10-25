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

class AdminUserManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }
    /**
     * 添加管理员
     * @param $data
     * @return AdminUser
     */
    public function addAdminUser($data)
    {
        $group = $this->entityManager->getRepository(AdminUserGroup::class)->findOneByAdminGroupId($data['adminGroupId']);

        $adminUser = new AdminUser();
        $data['adminAddTime']   = time();
        $adminUser->valuesSet($data);
        $adminUser->setGroup($group);

        $this->entityManager->persist($adminUser);

        $this->entityManager->flush();
        return $adminUser;
    }

    /**
     * 更新管理员信息
     * @param $user
     * @param $data
     * @return bool
     */
    public function updateAdminUser(AdminUser $user, $data)
    {
        $group = $this->entityManager->getRepository(AdminUserGroup::class)->findOneByAdminGroupId($data['adminGroupId']);

        if($user->getAdminEmail() != $data['adminEmail']) $user->setAdminEmail($data['adminEmail']);
        if($user->getAdminGroupId() != $data['adminGroupId']) {
            $user->setAdminGroupId($data['adminGroupId']);
            $user->setGroup($group);
        }

        $user->setAdminState($data['adminState']);

        $this->entityManager->flush();
        return true;
    }

    /**
     * 重置密码
     * @param AdminUser $user
     * @param array $data
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeAdminPassword(AdminUser $user, array $data)
    {
        $adminUser = new AdminUser();
        $adminUser->setAdminPassword($data['adminPassword']);

        if($user->getAdminPassword() != $adminUser->getAdminPassword()) {
            $user->setAdminPassword($data['adminPassword']);

            $this->entityManager->flush();
        }

        return true;
    }

    /**
     * 删除账户
     * @param $user
     */
    public function deleteUser($user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}