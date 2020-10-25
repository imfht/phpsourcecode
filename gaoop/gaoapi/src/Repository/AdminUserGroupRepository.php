<?php

namespace App\Repository;

use App\Entity\AdminModule;
use App\Entity\AdminUserGroup;
use App\Service\Redis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AdminUserGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminUserGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminUserGroup[]    findAll()
 * @method AdminUserGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminUserGroupRepository extends ServiceEntityRepository
{
    public $redis;

    public function __construct(ManagerRegistry $registry, Redis $redis)
    {
        parent::__construct($registry, AdminUserGroup::class);

        $this->redis = $redis->redis;
    }

    // /**
    //  * @return AdminUserGroup[] Returns an array of AdminUserGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AdminUserGroup
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * User: Gao
     * Date: 2020/3/21
     * Description: 获取用户组关联的模块集合
     * @param $id
     * @return array|mixed
     */
    public function getAdminModuleSonataAdmins($id)
    {
        $admin_modules = [];

        $obj = $this->find($id);
        if (is_object($obj)) {
            $module_ids = json_decode($obj->getModuleIds(), true);
            $admin_modules = $this->createQueryBuilder('a')
                ->select('a.sonata_admin')
                ->where('a.status = :status')
                ->andWhere('a.id in (:ids)')
                ->andWhere('a.pid != 0')
                ->setParameter('status', true)
                ->setParameter('ids', $module_ids)
                ->getQuery()
                ->getArrayResult();
        }

        return $admin_modules;
    }

    /**
     * User: Gao
     * Date: 2020/3/21
     * Description: 获取指定用户组对应模块集
     * @param AdminUserGroup $admin_user_group
     * @param bool $is_refresh
     * @return array|mixed
     */
    public function getAdminModuleByAdminUserGroup(AdminUserGroup $admin_user_group, $is_refresh = false)
    {
        $result = [];

        $redis_key = AdminUserGroup::REDIS_ADMIN_USER_GROUP__KEY . $admin_user_group->getId();
        $redis_admin_user_group_data = $this->redis->get($redis_key);
        if (is_null($redis_admin_user_group_data) || $is_refresh) {
            $admin_module_ids = json_decode($admin_user_group->getModuleIds(), true);
            $result = $this->getEntityManager()->getRepository(AdminModule::class)->getModules($admin_module_ids);
            $this->redis->setex($redis_key, 600, serialize($result));
        } else {
            $result = unserialize($redis_admin_user_group_data);
        }

        return $result;
    }

    /**
     * User: Gao
     * Date: 2020/3/22
     * Description: 获取对应用户组关联的用户数
     * @param array $id
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAdminUserCount($id = [])
    {
        $result = $this->getEntityManager()
            ->createQuery('SELECT count(a) as count FROM App\Entity\AdminUser a where a.admin_user_group_id in (:admin_user_group_id)')
            ->setParameter('admin_user_group_id', $id)
            ->getSingleResult();

        return $result['count'] ?? 0;
    }

    /**
     * User: Gao
     * Date: 2020/3/22
     * Description: 获取对应的admin_module中的sonata_admin集合
     * @param $id
     * @param bool $is_refresh
     * @return array|mixed
     */
    public function getSonataAdminById($id, $is_refresh = false)
    {
        $result = [];

        $admin_user_group = $this->find($id);
        if (is_object($admin_user_group)) {
            $redis_key = AdminUserGroup::REDIS_ADMIN_USER_GROUP__KEY . $id . ':sonata_admin';
            $redis_sonata_admin_data = $this->redis->get($redis_key);
            if (is_null($redis_sonata_admin_data) || $is_refresh) {
                $ids = json_decode($admin_user_group->getModuleIds(), true);
                $result = $this->getEntityManager()->getRepository(AdminModule::class)->getSonataAdmins($ids);
                $this->redis->setex($redis_key, 600, serialize($result));
            } else {
                $result = unserialize($redis_sonata_admin_data);
            }
        }

        return $result;
    }
}
