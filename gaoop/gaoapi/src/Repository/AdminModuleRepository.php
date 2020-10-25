<?php

namespace App\Repository;

use App\Entity\AdminModule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AdminModule|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminModule|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminModule[]    findAll()
 * @method AdminModule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminModule::class);
    }

    // /**
    //  * @return AdminModule[] Returns an array of AdminModule objects
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
    public function findOneBySomeField($value): ?AdminModule
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
     * Description: 获取指定模块集合
     * @param array $ids
     * @param bool $is_source_module
     * @return array
     */
    public function getModules($ids = [], $is_source_module = true)
    {
        $result = [];

        $query = $this->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', true);
        if (count($ids) > 0 || $is_source_module) {
            $query->andWhere('a.id IN (:ids)')->setParameter('ids', $ids);
        }
        $objs = $query->getQuery()->getArrayResult();

        // 整合成id为键的数组
        $items = [];
        foreach ($objs as $module) {
            $items[$module['id']] = $module;
        }

        // 通过引用实现无限分类
        foreach ($items as $key => $module) {
            if (isset($items[$module['pid']])) {
                $items[$module['pid']]['children'][] = &$items[$key];
                $items[$module['pid']]['sonata_admin'][] = $module['sonata_admin'];
            } else {
                $items[$key]['children'] = [];
                $items[$key]['sonata_admin'] = [];
                $result[$items[$key]['sort']] = &$items[$key];
            }
        }

        // children排序处理
        foreach ($result as &$module) {
            $sort_array = [];
            foreach ($module['children'] as $children) {
                $sort_array[$children['sort']] = $children;
            }
            ksort($sort_array);
            $module['children'] = $sort_array;
        }

        ksort($result);

        return $result;
    }

    /**
     * User: Gao
     * Date: 2020/3/21
     * Description: 获取用户组关联的模块集合
     * @param $ids
     * @return array|mixed
     */
    public function getSonataAdmins($ids = [])
    {
        $result = [];

        $objs = $this->createQueryBuilder('a')
            ->select('a.sonata_admin')
            ->where('a.status = :status')
            ->andWhere('a.id in (:ids)')
            ->andWhere('a.pid != 0')
            ->setParameter('status', true)
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getArrayResult();
        foreach ($objs as $obj) {
            array_push($result, $obj['sonata_admin']);
        }

        return $result;
    }
}
