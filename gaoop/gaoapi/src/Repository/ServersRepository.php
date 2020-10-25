<?php

namespace App\Repository;

use App\Entity\Servers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Servers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Servers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Servers[]    findAll()
 * @method Servers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Servers::class);
    }

    // /**
    //  * @return Servers[] Returns an array of Servers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Servers
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
