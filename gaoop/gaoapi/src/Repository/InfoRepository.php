<?php

namespace App\Repository;

use App\Entity\Info;
use App\Library\Helper\GetterHelper;
use App\Service\Redis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Info|null find($id, $lockMode = null, $lockVersion = null)
 * @method Info|null findOneBy(array $criteria, array $orderBy = null)
 * @method Info[]    findAll()
 * @method Info[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoRepository extends ServiceEntityRepository
{
    public $redis;

    public function __construct(ManagerRegistry $registry, Redis $redis)
    {
        $this->redis = $redis;
        parent::__construct($registry, Info::class);
    }

    public function getInfoList($page, $limit = 10)
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT l FROM App\Entity\Info l order by l.id asc')
            ->setFirstResult($page - 1)
            ->setMaxResults($page * $limit);
        $paginator = GetterHelper::getService('knp_paginator');
        return $paginator->paginate($query, $page, $limit);
    }

    /**
     * User: Gao
     * Date: 2020/2/28
     * Description: 获取当前操作的元数据
     * @return Info|mixed|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getCurrentInfo()
    {
        $result = null;

        $redis_info = $this->redis->get(Info::REDIS_CURRENT_INFO_KEY);
        if (is_null($redis_info)) {
            $info = $this->findOneBy(['isCurrent' => true], ['id' => 'desc']);
            if (!is_object($info)) {
                $info = $this->findOneBy([], ['id' => 'desc']);
                if (is_object($info)) {
                    $info->setIsCurrent(true);
                    $this->getEntityManager()->flush();
                }
            }
            $result = $info;
            $this->redis->setex(Info::REDIS_CURRENT_INFO_KEY, 600, serialize($info));
        } else {
            $result = unserialize($redis_info);
        }

        return $result;
    }

    /**
     * User: Gao
     * Date: 2020/2/29
     * Description: 更新当前info版本信息
     * @param $info_id
     * @param $version
     */
    public function updateCurrentInfoVersion($info_id, $version)
    {
        $this->getEntityManager()->beginTransaction();
        $numUpdated = $this->getEntityManager()->createQuery('update App\Entity\Info p set p.version = :version where p.id = :id')
            ->setParameter('version', $version)
            ->setParameter('id', $info_id)
            ->execute();
        if ($numUpdated) {
            // 更新redis中info数据
            $redis_info = $this->redis->get(Info::REDIS_CURRENT_INFO_KEY);
            if (is_null($redis_info)) {
                $info = $this->find($info_id);
                $this->redis->setex(Info::REDIS_CURRENT_INFO_KEY, 600, serialize($info));
            } else {
                $obj = unserialize($redis_info);
                $obj->setVersion($version);
                $this->redis->setex(Info::REDIS_CURRENT_INFO_KEY, 600, serialize($obj));
            }
            $this->getEntityManager()->commit();
        } else {
            $this->getEntityManager()->rollback();
        }
    }
}
