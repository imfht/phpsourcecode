<?php

namespace App\Repository;

use App\Entity\Parameters;
use App\Library\Helper\GeneralHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Parameters|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parameters|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parameters[]    findAll()
 * @method Parameters[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParametersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parameters::class);
    }

    // /**
    //  * @return Parameters[] Returns an array of Parameters objects
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
    public function findOneBySomeField($value): ?Parameters
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
     * Date: 2019/11/18
     * Description: 插入参数
     * @param $paths_id
     * @param array $data
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function batchInsert($paths_id, $data = [])
    {
        $batchSize = 20;
        $i = 0;
        $version = date('YmdHis');
        $info = GeneralHelper::getOneInstance()->getCurrentInfo();
        foreach ($data as $category => $old_items) {
            foreach ($old_items as $item) {
                // 如果未填写字段名则视为无效字段 or 参数类型不存在
                $category_id = array_search($category, Parameters::$categories);
                if (empty($item['key']) || !$category_id) {
                    continue;
                }

                $parameters = new Parameters();
                $parameters->setInfoId($info->getId());
                $parameters->setPathsId($paths_id);
                $parameters->setName($item['key']);
                $parameters->setCategory($category_id);
                $parameters->setDescription($item['description']);
                $parameters->setRequired(isset($item['required']) ? true : false);
                $parameters->setFormat($item['format']);
                $parameters->setVersion($version);
                $this->getEntityManager()->persist($parameters);
                if (($i % $batchSize) === 0) {
                    $this->getEntityManager()->flush();
                    $this->getEntityManager()->clear();
                }
                $i++;
            }
        }
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    public function getItemByPathId($path_id)
    {
        return $this->createQueryBuilder('t')
            ->select('t.category')
            ->addSelect('t.name')
            ->addSelect('t.required')
            ->addSelect('t.format')
            ->addSelect('t.description')
            ->where('t.pathsId = :pathsId')
            ->andWhere('t.status = :status')
            ->setParameter('pathsId', $path_id)
            ->setParameter('status', true)
            ->getQuery()
            ->getResult();
    }
}
