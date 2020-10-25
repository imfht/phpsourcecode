<?php

namespace App\Repository;

use App\Entity\Log;
use App\Library\Helper\GetterHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\Paginator;

/**
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    /**
     * User: Gao
     * Date: 2020/2/21
     * Description: 日志查询
     * @param $info_id
     * @param $version
     * @param $path
     * @param $action
     * @param $page
     * @param int $limit
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getVersionList($info_id, $version, $path, $action, $page, $limit = 10)
    {
        $filter_where_sql = '';
        if ($version != '') {
            $filter_where_sql = ' AND l.version = :version';
        }
        if ($path != '') {
            $filter_where_sql .= ' AND l.path like :path';
        }
        if ($action != '') {
            $filter_where_sql .= ' AND l.action = :action';
        }

        // 单独计算总数，修正分页bundle bug
        $count_query = $this->getEntityManager()
            ->createQuery('SELECT COUNT(DISTINCT(l.version)) FROM App\Entity\Log l WHERE l.info_id = :info_id' . $filter_where_sql)
            ->setParameter('info_id', $info_id);
        if ($version != '') {
            $count_query->setParameter('version', $version);
        }
        if ($path != '') {
            $count_query->setParameter('path', '%' . $path . '%');
        }
        if ($action != '') {
            $count_query->setParameter('action', $action);
        }
        $count = $count_query->getSingleScalarResult();

        // 查询数据
        $query = $this->getEntityManager()
            ->createQuery('SELECT DISTINCT(l.version) FROM App\Entity\Log l WHERE l.info_id = :info_id ' . $filter_where_sql . ' order by l.id desc')
            ->setParameter('info_id', $info_id)
            ->setFirstResult($page - 1)
            ->setMaxResults($page * $limit)
            ->setHint('knp_paginator.count', $count);
        if ($version != '') {
            $query->setParameter('version', $version);
        }
        if ($path != '') {
            $query->setParameter('path', '%' . $path . '%');
        }
        if ($action != '') {
            $query->setParameter('action', $action);
        }
        $paginator = GetterHelper::getService('knp_paginator');
        $pagination = $paginator->paginate($query, $page, $limit, ['distinct' => false]);

        $items = $pagination->getItems();
        $new_items = [];
        foreach ($items as $item) {
            $_item = [
                'version' => $item[1],
                'data' => $this->getVersionItemTop($info_id, $item[1], $path, $action)
            ];
            array_push($new_items, $_item);
        }
        $pagination->setItems($new_items);

        return $pagination;
    }

    /**
     * User: Gao
     * Date: 2020/2/21
     * Description: 获取指定版本最新的三条数据
     * @param $info_id
     * @param $version
     * @param $path
     * @param $action
     * @return mixed\
     */
    public function getVersionItemTop($info_id, $version, $path, $action)
    {
        $query = $this->createQueryBuilder('l')
            ->where('l.info_id = :info_id')
            ->andWhere('l.version = :version')
            ->orderBy('l.id', 'desc')
            ->setParameter('info_id', $info_id)
            ->setParameter('version', $version)
            ->setMaxResults(3);
        if ($path != '') {
            $query->andWhere('l.path like :path');
            $query->setParameter('path', '%' . $path . '%');
        }
        if ($action != '') {
            $query->andWhere('l.action = :action');
            $query->setParameter('action', $action);
        }

        return $query->getQuery()->getResult();
    }

    public function getSingleVersionList($info_id, $version, $path, $action, $page, $limit = 10)
    {
        $filter_where_sql = '';
        if ($path != '') {
            $filter_where_sql .= ' AND l.path like :path';
        }
        if ($action != '') {
            $filter_where_sql .= ' AND l.action = :action';
        }
        $query = $this->getEntityManager()
            ->createQuery('SELECT l FROM App\Entity\Log l WHERE l.info_id = :info_id AND l.version = :version ' . $filter_where_sql . ' order by l.id desc')
            ->setParameter('info_id', $info_id)
            ->setParameter('version', $version)
            ->setFirstResult($page - 1)
            ->setMaxResults($page * $limit);
        if ($path != '') {
            $query->setParameter('path', '%' . $path . '%');
        }
        if ($action != '') {
            $query->setParameter('action', $action);
        }
        $paginator = GetterHelper::getService('knp_paginator');
        $pagination = $paginator->paginate($query, $page, $limit);
        $new_items[] = [
            'version' => $version,
            'data' => $pagination->getItems()
        ];
        $pagination->setItems($new_items);
        return $pagination;
    }

}
