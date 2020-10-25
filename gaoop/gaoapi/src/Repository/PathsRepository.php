<?php

namespace App\Repository;

use App\Entity\Paths;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Paths|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paths|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paths[]    findAll()
 * @method Paths[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PathsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paths::class);
    }

    // /**
    //  * @return Paths[] Returns an array of Paths objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Paths
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getPathsToArray($info_id)
    {
        return $this->createQueryBuilder('t')
            ->select('t.id')
            ->addSelect('t.tagId')
            ->addSelect('t.summary')
            ->addSelect('t.operationId')
            ->where('t.infoId = :info_id')
            ->andWhere('t.status = :status')
            ->setParameter('info_id', $info_id)
            ->setParameter('status', true)
            ->getQuery()
            ->getResult();
    }

    public function findOneToArray($id)
    {
        return $this->createQueryBuilder('t')
            ->select('t.id')
            ->addSelect('t.url')
            ->addSelect('t.methodId as method')
            ->addSelect('t.summary')
            ->addSelect('t.description')
            ->addSelect('t.success_code')
            ->addSelect('t.remark')
            ->where('t.id = :id')
            ->andWhere('t.status = :status')
            ->setParameter('id', $id)
            ->setParameter('status', true)
            ->getQuery()
            ->getSingleResult();
    }
}
