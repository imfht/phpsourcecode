<?php

namespace App\Repository;

use App\Entity\Methods;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Methods|null find($id, $lockMode = null, $lockVersion = null)
 * @method Methods|null findOneBy(array $criteria, array $orderBy = null)
 * @method Methods[]    findAll()
 * @method Methods[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MethodsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Methods::class);
    }

    // /**
    //  * @return Methods[] Returns an array of Methods objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Methods
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * User: gao
     * Date: 2019/11/16
     * Description: 返回键值对数据
     * @return array
     */
    public function getAllDataByArray(): array
    {
        $result = [];

        $items = self::findAll();
        foreach ($items as $item) {
            $result[$item->getId()] = strtolower($item->getValue());
        }

        return $result;
    }
}
