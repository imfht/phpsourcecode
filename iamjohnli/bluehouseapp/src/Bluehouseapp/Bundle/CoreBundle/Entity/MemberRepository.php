<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 14-10-10
 * Time: 上午9:42
 */

namespace Bluehouseapp\Bundle\CoreBundle\Entity;

use Bluehouseapp\Bundle\CoreBundle\Doctrine\ORM\EntityRepository;

class MemberRepository  extends EntityRepository
{

    public function queryUserByLockedPaginator($locked)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        $queryBuilder
            ->orderBy('m.modified', 'desc')
            ->andWhere('m.locked = :locked')
            ->setParameter('locked', $locked)
        ;

        return $this->getPaginator($queryBuilder);
    }

    public function countUserByLocked( $locked)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();
        $queryBuilder
            ->select('COUNT(m)')
        ->where('m.locked = :locked')
        ->setParameter('locked', $locked);
       return $queryBuilder->getQuery()->getSingleScalarResult();
    }
    protected function getAlias()
    {
        return 'm';
    }
} 