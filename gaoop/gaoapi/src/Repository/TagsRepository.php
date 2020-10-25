<?php

namespace App\Repository;

use App\Entity\Tags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Tags|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tags|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tags[]    findAll()
 * @method Tags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tags::class);
    }

    // /**
    //  * @return Tags[] Returns an array of Tags objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tags
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * User: gao
     * Date: 2019/11/29
     * Description: ~
     * @param $info_id
     * @return array
     */
    public function getAllDataByArray($info_id): array
    {
        $result = [];

        $items = self::findBy(['infoId' => $info_id]);
        foreach ($items as $item) {
            $result[$item->getId()] = $item->getName();
        }

        return $result;
    }

    /**
     * User: Gao
     * Date: 2019-11-30
     * Description: ~
     * @param $info_id
     * @return array
     */
    public function getTagIds($info_id)
    {
        $result = [];

        $items = self::findBy(['infoId' => $info_id]);
        foreach ($items as $item) {
            array_push($result, $item->getId());
        }

        return $result;
    }

    public function getTagsToArray($info_id)
    {
        return $this->createQueryBuilder('t')
            ->select('t.id')
            ->addSelect('t.name')
            ->addSelect('t.description')
            ->where('t.infoId = :info_id')
            ->setParameter('info_id', $info_id)
            ->getQuery()
            ->getResult();
    }
}
